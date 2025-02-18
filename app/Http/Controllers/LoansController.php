<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Loans;
use App\Models\Referee;
use App\Models\Savings;
use App\Models\Settings;
use Illuminate\Http\Request;
use function PHPSTORM_META\type;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreLoansRequest;
use App\Http\Requests\UpdateLoansRequest;
use App\Notifications\RefereeNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LoanApprovedNotification;

use App\Notifications\LoanCreationNotification;
use App\Notifications\LoanRejectedNotification;

class LoansController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    // dd($request->route('status'));
    $loanStatus = $request->route('status'); 
    $userLoans = $request->route('user');
    $user = Auth::user();
    $role = session('role');

    $query = Loans::query();

    if (!empty($loanStatus)) {
        $query->where('status', $loanStatus);
    }

    if (!empty($userLoans)) {
        $query->where('user_id', $userLoans);
    }

    if ($role === 'admin' || $role === 'superadmin' || $role === 'staff') {
        $loans = $query->paginate(15);
    } else {
        $loans = $query->where('user_id', $user->id)->paginate(15);
    }

    return view('loans.index', compact('loans'));
}


   // In your LoansController

/**
 * Show the form for creating a new resource.
 */
    public function create()
    {
        $settings = Settings::first();
        $minSavings = $settings->min_savings_guarantor ?? 0;
        $allowGuarantor = $settings->allow_guarantor; // true/false flag
        $minGuarantors = $allowGuarantor ? $settings->min_guarantor : 0; 

        $user = Auth::user();

        // Get all savings accounts (with their user) that have sufficient balance and are not the current user.
        $savings = Savings::with('user')
            ->where('account_balance', '>=', $minSavings)
            ->where('user_id', '!=', $user->id)
            ->get();

        // If no eligible savings accounts are found, return a message.
        if ($savings->isEmpty()) {
            $referee = ['referee' => 'No referee contact your admin'];
            $no = null;
            return view('loans.create', compact('referee', 'no', 'allowGuarantor', 'minGuarantors'));
        }

        $referee = $savings;
        $no = true;
        return view('loans.create', compact('referee', 'no', 'allowGuarantor', 'minGuarantors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $settings = Settings::first();
        $user = Auth::user();
        $savings = $user->savings; // Assumes one-to-one relation
        $accountBalance = $savings->account_balance;

        // Determine maximum loan amount based on loan type
        if ($settings->loan_type === 'fixed') {
            $loanAmount = $settings->loan_max_amount;
        } else {
            $loanAmount = $accountBalance * $settings->loan_max_amount;
        }
        $loanDuration = $settings->loan_duration;
        $interestRate = $settings->interest_rate;

        // Build the base validation rules.
        $rules = [
            'loanAmount'      => 'required|numeric|min:1|max:' . $loanAmount,
            'loanDuration'    => 'required|integer|min:1|max:' . $loanDuration,
            'interestRate'    => 'required|numeric|min:0|max:' . $interestRate,
            'principalAmount' => 'required|numeric|min:0',
            'monthlyPayments' => 'required|numeric|min:0',
            'description'     => 'required|string|max:255',
        ];

        // If guarantors are allowed, require at least the minimum number of referee fields.
        if ($settings->allow_guarantor) {
            $minGuarantors = $settings->min_guarantor;
            for ($i = 0; $i < $minGuarantors; $i++) {
                $rules['referee_' . $i] = 'required|exists:users,id';
            }
            // Optionally, validate additional referee fields (if any) as nullable.
            // Count all referee_* fields in the request.
            $refereeCount = count(array_filter($request->all(), function ($key) {
                return strpos($key, 'referee_') === 0;
            }, ARRAY_FILTER_USE_KEY));
            for ($i = $minGuarantors; $i < $refereeCount; $i++) {
                $rules['referee_' . $i] = 'nullable|exists:users,id';
            }
        }

        $validated = $request->validate($rules);

        // check if the user has an active loan if yes return an error
        $activeLoan = Loans::where('user_id', $user->id)
                   ->where(function($query) {
                       $query->where('status', 'approved')
                             ->orWhere('status', 'disbursed')
                             ->orWhere('status', 'pending');
                   })
                   ->first();

        // dd($activeLoan, $user);
        if ($activeLoan) {
            return redirect()->route('loans.index')->with('error', 'You have either an active loan or a pending loan application!');
        }                                                                    

        // Create the loan record.
        $loan = Loans::create([
            'loan_amount'      => $validated['principalAmount'],
            'loan_duration'    => $validated['loanDuration'],
            'interest_rate'    => $validated['interestRate'],
            'principal_amount' => $validated['loanAmount'],
            'monthly_payments' => $validated['monthlyPayments'],
            'description'      => $validated['description'],
            'user_id'          => $user->id,
        ]);

        // If guarantors are allowed, loop through all referee inputs.
        if ($settings->allow_guarantor) {
            foreach ($validated as $key => $value) {
                if (strpos($key, 'referee_') === 0 && !empty($value)) {
                    Referee::create([
                        'loan_id' => $loan->id,
                        'user_id' => $value,
                    ]);
                    $refereeUser = User::find($value);
                    $refereeUser->notify(new RefereeNotification($loan, $refereeUser->name));
                }
            }
        }

        // Notify the user with a delay of 3 minutes
        (new LoanCreationNotification($loan))->delay(now()->addMinutes(3));

        return redirect()->route('loans.index')->with('success', 'Loan created and referees notified successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Loans $loan)
{
    // Load the loan with its referees and their users
    $loan = Loans::with('referee.user')->findOrFail($loan->id);

    // Get the role from the session
    $role = session('role');
    // Authenticate the user if they are the owner of the loan, an admin, or a referee
    if (Auth::id() !== $loan->user_id && !in_array($role, ['admin', 'superadmin'])) {
        $referees = $loan->referee->pluck('user_id')->toArray();
        if (!in_array(Auth::id(), $referees)) {
            return redirect()->route('loans.index')->with('error', 'You are not authorized to view this loan!');
        }
    }

    // Return the loan view with the loan data
    return view('loans.show', compact('loan'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loans $loans)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request, $loans)
    {
        // dd($request->all());
        $referee = $request->route('referee');
        
        $action = $request->route('action');

        $loans = Loans::findOrFail($loans);
        if($referee){
            $referee = Referee::findOrFail($referee);
        }
        $admin = false;
        if(!$referee){
            $admin = true;
        }
        // dd('Referee: ' . $referee, 'Admin: ' . $admin, 'Action: ' . $action, $loans);


        if ($action === 'approve') {
            return $this->approve($loans, $referee, $admin);
        }
        if ($action === 'reject') {
            return $this->reject($loans, $referee, $admin);
        }
        return redirect()->back()->with('error', 'Invalid action!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loans $loans)
    {
        //
    }

    private function sendEmail($loan, $referee)
    {
        Mail::send('emails.referee', ['loan' => $loan], function ($message) use ($referee) {
            $message->to($referee->email, $referee->name)->subject('Loan Notification');
        });
    }

    private function approve(Loans $loan, Referee $referee = null, $admin = false)
{
    DB::beginTransaction();

    try {
        $borrower = $loan->user;

        // Get all admin and superadmin users
        $adminUsers = User::where('role', 'admin')
                          ->orWhere('role', 'superadmin')
                          ->get();

        if ($admin) {
    // Retrieve the count of approved and total referees
    $approvedReferees = $loan->referee()->where('approved', true)->count();
    $totalReferees = $loan->referee()->count();

    // Check if approved referees are less than total referees
    if ($approvedReferees < $totalReferees) {
        return redirect()->back()->with('error', 'Loan approval failed. Not all referees have approved the loan!');
    }

    // If all referees have approved, proceed with approval
    if ($approvedReferees === $totalReferees && (Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin')) {
        $loan->update(['status' => 'approved', 'approved_at' => now(), 'approved_by' => Auth::id()]);

        // Notify Borrower with a delay of 3 minutes
        $borrower->notify((new LoanApprovedNotification($loan, 'borrower', null, 'admin'))->delay(now()->addMinutes(3)));

        // Notify all Admins and Superadmins with a delay of 3 minutes
        foreach ($adminUsers as $adminUser) {
            $adminUser->notify((new LoanApprovedNotification($loan, 'admin', null, 'admin'))->delay(now()->addMinutes(3)));
        }

        DB::commit();
        return redirect()->back()->with('success', 'Loan approved successfully!');
    }
}


        if ($referee) {
            $approveReferral = $loan->referee()->where('id', '=',$referee->id)->update(['approved' => true]);

            if ($approveReferral) {
                // ✅ Notify Borrower with a delay of 3 minutes
                $borrower->notify((new LoanApprovedNotification($loan, 'borrower', $referee, 'referee'))->delay(now()->addMinutes(3)));

                // ✅ Notify Referee with a delay of 3 minutes
                $referee->user->notify((new LoanApprovedNotification($loan, 'referee', $referee, 'referee'))->delay(now()->addMinutes(3)));

                // ✅ Notify All Admins and Superadmins with a delay of 3 minutes
                foreach ($adminUsers as $adminUser) {
                    $adminUser->notify((new LoanApprovedNotification($loan, 'admin', $referee, 'referee'))->delay(now()->addMinutes(3)));
                }

                DB::commit();
                return redirect()->back()->with('success', 'Loan approved successfully!');
            }
        }

        dd('here');
        DB::rollBack();
        return redirect()->back()->with('error', 'Loan approval failed. Not all referees have approved the loan!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}

    private function reject(Loans $loan, Referee $referee = null, $admin = false)
    {
        DB::beginTransaction();

        try {
            $borrower = $loan->user;

            // Get all admin and superadmin users
            $adminUsers = User::where('role', 'admin')
                            ->orWhere('role', 'superadmin')
                            ->get();

            if ($admin) {
                // $approvedReferees = $loan->referee()->where('approved', true)->count();
                // $totalReferees = $loan->referee()->count();

                // if ($approvedReferees < $totalReferees) {
                //     return redirect()->back()->with('error', 'Loan approval failed. Not all referees have approved the loan!');
                // }

                if (Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin') {
                    $loan->update(['status' => 'rejected']);  // Use 'rejected' status

                    // ✅ Notify Borrower with a delay of 3 minutes
                    $borrower->notify((new LoanRejectedNotification($loan, 'borrower', null, 'admin'))->delay(now()->addMinutes(3)));

                    // ✅ Notify All Admins and Superadmins with a delay of 3 minutes
                    foreach ($adminUsers as $adminUser) {
                        $adminUser->notify((new LoanRejectedNotification($loan, 'admin', null, 'admin'))->delay(now()->addMinutes(3)));
                    }

                    DB::commit();
                    return redirect()->back()->with('success', 'Loan rejected successfully!');
                }
            }

            if ($referee) {
                $approveReferral = $loan->referee()->where('id', '=', $referee->id)->update(['approved' => false]);

                if ($approveReferral) {
                    // ✅ Notify Borrower with a delay of 3 minutes
                    $borrower->notify((new LoanRejectedNotification($loan, 'borrower', $referee, 'referee'))->delay(now()->addMinutes(3)));

                    // ✅ Notify Referee with a delay of 3 minutes
                    $referee->user->notify((new LoanRejectedNotification($loan, 'referee', $referee, 'referee'))->delay(now()->addMinutes(3)));

                    // ✅ Notify All Admins and Superadmins with a delay of 3 minutes
                    foreach ($adminUsers as $adminUser) {
                        $adminUser->notify((new LoanRejectedNotification($loan, 'admin', $referee, 'referee'))->delay(now()->addMinutes(3)));
                    }

                    DB::commit();
                    return redirect()->back()->with('success', 'Loan rejected successfully!');
                }
            }

            DB::rollBack();
            return redirect()->back()->with('error', 'Loan approval failed. Not all referees have approved the loan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());  // Log the error message for easier debugging
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

}
