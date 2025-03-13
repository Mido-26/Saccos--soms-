<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Loans;
use App\Models\Savings;
use App\Models\Settings;
use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\LoanRepayments;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTransactionsRequest;
use App\Http\Requests\UpdateTransactionsRequest;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $role = session('role');
    $query = Transactions::query();
    $user = $request->route('user');
    if ($role == 'admin' || $role == 'staff'){
        if(!empty($user)){
            $query->where('user_id', '=', $user);
        }

    }else{
        $id = Auth::user()->id;
        $query->where('user_id', '=', $id);
    }
    // $query = Transactions::query();


    // Paginate and get filtered results
    $transactions = $query->orderBy('id', 'desc')->paginate(10);
    // dd($transactions);
    $total = 0;
        foreach ($transactions as $transaction) {
            $total = $total + $transaction->amount;
        }
    // Pass the request inputs to the view to retain selected filters
    return view('Transactions.index', compact('transactions', 'total'))->withInput($request->all());
}


    public function create()
    {
        if (!Auth::user()->can('viewAdminDashboard')) {
            abort(403);
        }

        // Retrieve only approved loans
        $loans = Loans::where('status', 'approved')->get();

        // Retrieve all user IDs
        $ids = User::pluck('id')->toArray();

        // Retrieve users with only first_name, last_name, and id, including their savings
        $users = User::select('id', 'first_name', 'last_name')->with('savings')->get();

        return view('Transactions.create', compact('users', 'ids', 'loans'));
    }


    public function store(Request $request)
{
    // Only admins can perform transactions.
    // if (Auth::user()->role !== 'admin' ) {
    //     return redirect()->route('unauthorized');
    // }
    if (!Auth::user()->can('viewAdminDashboard')) {
        return redirect()->route('unauthorized');
    }

    $setting = Settings::first();

    // Retrieve the minimum saving amount from the settings.
    $minSavingAmount = $setting->min_savings;

    // Set up base validation rules.
    $approvedLoans = Loans::where('status', 'approved')->pluck('id')->implode(',');

    $rules = [
        'user_id'        => 'required|exists:users,id',
        'type'           => 'required|string|in:savings_deposit,savings_withdrawal,loan_payment,loan_disbursement',
        'loans'          => [
            'nullable',
            'exists:loans,id',
            Rule::in(explode(',', $approvedLoans)),
            Rule::requiredIf(fn() => request('type') === 'loan_disbursement'),
        ],
        'amount'         => [
            'nullable',
            'numeric',
            'min:'.$minSavingAmount,
            Rule::requiredIf(fn() => request('type') !== 'loan_disbursement'),
        ],
        'description'    => 'nullable|string',
        'payment_method' => 'required|string',
    ];

    // Add extra rules for loan-specific fields.
    if ($request->type === 'loan_payment') {
        $rules['repayment_date'] = 'nullable|date';
    }
    if ($request->type === 'loan_disbursement') {
        $rules['disbursement_note'] = 'nullable|string';
    }

    $validated = $request->validate($rules);

    DB::beginTransaction();

    try {
        // Retrieve the user.
        $user = User::findOrFail($validated['user_id']);

        // Process deposit and withdrawal transactions: update savings.
        if (in_array($validated['type'], ['savings_deposit', 'savings_withdrawal'])) {
            $savingsAccount = $user->savings; // Assumes a one-to-one relation: User->savings

            if ($validated['type'] === 'savings_deposit') {
                $savingsAccount->account_balance += $validated['amount'];
                $savingsAccount->last_deposit_date = now();
            } elseif ($validated['type'] === 'savings_withdrawal') {
                if ($savingsAccount->account_balance < $validated['amount']) {
                    return redirect()->back()->withErrors(['error' => 'Insufficient balance for withdrawal.']);
                }
                $savingsAccount->account_balance -= $validated['amount'];
                $savingsAccount->last_deposit_date = now();
            }
            $savingsAccount->save();
        }
        // Process a loan repayment.
        elseif ($validated['type'] === 'loan_payment') {
            // Look for the user's disbursed (active) loan.
            $loan = $user->loan()->where('status', 'disbursed')->first();
            if (!$loan) {
                return redirect()->back()->withErrors(['error' => 'No disbursed loan found for repayment.']);
            }

            // Create a repayment installment record.
            LoanRepayments::create([
                'loan_id'        => $loan->id,
                'amount'         => $validated['amount'],
                'repayment_date' => $validated['repayment_date'] ?? now(),
            ]);

            // Deduct the installment amount from the loan's outstanding balance.
            $loan->outstanding_amount -= $validated['amount'];

            // If the loan is fully repaid, mark its status as 'paid'.
            if ($loan->outstanding_amount <= 0) {
                $loan->status = 'paid';
                $loan->outstanding_amount = 0;
            }
            $loan->save();
        }
        // Process a loan disbursement.
        elseif ($validated['type'] === 'loan_disbursement') {
            $submittedLoan = Loans::where('id', $validated['loans'])->first();

            if (!$submittedLoan) {
                return redirect()->back()->with('error', 'Selected loan does not exist.');
            }

            // Check if loan status is 'approved'
            if ($submittedLoan->status !== 'approved') {
                return redirect()->back()->with('error', 'The loan is not approved for disbursement.');
            }

            // Set the amount to the principal amount of the loan
            $validated['amount'] = $submittedLoan->principal_amount;

            // Update the loan status to 'disbursed' and set disbursed_at timestamp
            $submittedLoan->update([
                'status'       => 'disbursed',
                'disbursed_at' => now(),
            ]);
        }



        // Optionally store extra loan-related fields in transaction metadata.
        $metadata = [];
        if ($validated['type'] === 'loan_payment') {
            $metadata['repayment_date'] = $validated['repayment_date'] ?? now();
        }
        if ($validated['type'] === 'loan_disbursement') {
            $metadata['disbursement_note'] = $validated['disbursement_note'] ?? null;
        }

        // Create the transaction record.
        $transaction = Transactions::create([
            'user_id'               => $validated['user_id'],
            'type'                  => $validated['type'],
            'amount'                => $validated['amount'],
            'description'           => $validated['description'] ?? null,
            'transaction_reference' => Transactions::generateTransactionReference(),
            'payment_method'        => $validated['payment_method'],
            'initiator_id'          => Auth::id(),
            'completed_at'          => now(),
            'metadata'              => !empty($metadata) ? json_encode($metadata) : null,
        ]);

        DB::commit();

        return redirect()->route('Transactions.index')
            ->with('success', 'Transaction processed successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
}



    public function show(Transactions $transaction)
{
    // Check if the user is not an admin or doesn't own the transaction
    if (Auth::user()->role !== 'admin' && $transaction->user_id !== Auth::id()) {
        return redirect()->route('unauthorized');
    }

    // Return the transaction details view
    return view('Transactions.show', compact('transaction'));
}


    public function edit(Transactions $transaction){
        $users = User::select('id', 'first_name', 'last_name')->with('savings')->get();
        return view('Transactions.edit', compact('users', 'transaction'));
    }


    public function update(Request $request, $id)
{
    if (Auth::user()->role !== 'admin') {
        return redirect()->route('unauthorized');
    }
    // dd($request->all());
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'type' => 'required|string',
        'amount' => 'required|numeric|min:20000',
        'description' => 'nullable|string',
        'payment_method' => 'required|string',
    ]);

    // Start a database transaction
    DB::beginTransaction();

    try {
        // Fetch the existing transaction and associated savings account
        $transaction = Transactions::findOrFail($id);
        // $savingsAccount = Savings::findOrFail($validated['user_id']);
        $userId = $request->user_id;
        $savingsAccount = $transaction->user->savings;
        // dd($savingsAccount);
        // Reverse the previous transaction impact on the account balance
        if ($transaction->type == 'savings_deposit') {
            $savingsAccount->account_balance -= $transaction->amount;
        } elseif ($transaction->type == 'savings_withdrawal') {
            $savingsAccount->account_balance += $transaction->amount;
        }

        // Update balance based on new transaction type and amount
        if ($request->type == 'savings_deposit') {
            $savingsAccount->account_balance += $request->amount;
            $savingsAccount->last_deposit_date = now();
        } elseif ($request->type == 'savings_withdrawal') {
            // check if user has active loan
            // $loan = $transaction->user->loan()->where('status', 'disbursed')->first();
            $loan = Loans::where('user_id', $userId)->where('status', 'disbursed')->first();
            if ($loan) {
                return redirect()->back()->withErrors(['error' => 'Cannot withdraw savings with an active loan.']);
            }
            if ($savingsAccount->account_balance >= $request->amount) {
                $savingsAccount->account_balance -= $request->amount;
            } else {
                return redirect()->back()->withErrors(['error' => 'Insufficient balance for withdrawal.']);
            }
        }

        // Save the updated savings account balance
        $savingsAccount->save();

        // Update transaction details
        $transaction->update([
            'amount' => $request->amount,
            'description' => $request->description,
            'type' => $request->type,
            'user_id' => $userId,
            'payment_method' => $validated['payment_method'],
        ]);

        // Commit the transaction
        DB::commit();

        // Redirect to transactions index with a success message
        return redirect()->route('Transactions.index')->with('success', 'Transaction updated successfully and balance adjusted.');
    } catch (\Exception $e) {
        // Rollback in case of any error
        DB::rollBack();

        // Redirect with error message
        return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
}
}
