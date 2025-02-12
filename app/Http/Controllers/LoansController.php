<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Loans;
use App\Models\Referee;
use App\Models\Savings;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreLoansRequest;
use App\Http\Requests\UpdateLoansRequest;
use App\Notifications\RefereeNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LoanApprovedNotification;
use App\Notifications\LoanRejectedNotification;

use function PHPSTORM_META\type;

class LoansController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $role = session('role'); 
        if ($role === 'admin' || $role === 'superadmin') {
            $loans = Loans::paginate(15);
            return view('loans.index',compact('loans'));
        }else{
            $loans = Loans::where('user_id', $user->id)->paginate(15);
            return view('loans.index',compact('loans'));
        }
        
        // return view('loans.index',compact('loans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $settin = Settings::first();
        $minSavings = $settin->min_savings_guarantor;
        $maxLoans = $settin->max_guarantor;
        $no = true;
        $user = Auth::user();

        // Get all savings accounts with a balance greater than or equal to $minSavings, excluding the current user
        $savings = Savings::with('user')
            ->where('account_balance', '>=', $minSavings)
            ->where('user_id', '!=', $user->id) // Exclude current user
            ->get();

        if ($savings->isEmpty()) {
            $referee = ['referee' => 'No referee contact your admin'];
            $no = null;
            return view('loans.create',compact('referee', 'no'));
        }        
        // Loop through savings to access user data.
        // foreach ($savings as $saving) {
        //     $referee = $saving->user; // Access the user directly via the relationship.
        //     // Do something with $user and $saving.
        // }
        $referee = $savings;
        // dd($referee);
        return view('loans.create',compact('referee', 'no'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // $settings = Settings::first();
    // $loanAmount = $settings->max_loan_amount;
    $validated = $request->validate([
        'loanAmount' => 'required|numeric|min:1|max:1000000',
        'loanDuration' => 'required|integer|min:1|max:60',
        'interestRate' => 'required|numeric|min:0|max:100',
        'principalAmount' => 'required|numeric|min:0',
        'monthlyPayments' => 'required|numeric|min:0',
        'description' => 'required|string|max:255',
        'referee_0' => 'required|exists:users,id',
        'referee_1' => 'required|exists:users,id',
        'referee_2' => 'required|exists:users,id',
    ]);

    $loan = Loans::create([
        'loan_amount' => $validated['principalAmount'],
        'loan_duration' => $validated['loanDuration'],
        'interest_rate' => $validated['interestRate'],
        'principal_amount' => $validated['loanAmount'],
        'monthly_payments' => $validated['monthlyPayments'],
        'description' => $validated['description'],
        'user_id' => Auth::id(),
    ]);

    foreach (['referee_0', 'referee_1', 'referee_2'] as $refereeKey) {
        Referee::create([
            'loan_id' => $loan->id,
            'user_id' => $validated[$refereeKey],
        ]);

        $referee = User::find($validated[$refereeKey]);
        $referee->notify(new RefereeNotification($loan, $referee->name));
    }

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
