<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Loans;
use App\Models\Savings;
use App\Models\Settings;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $user = Auth::user();
        // abort('520');

        // check if user has pending loan or active loan 
        $loans = Loans::where('user_id', $user->id)->where('status', '!=','completed')->first() ?? null;

        // get latest 3 transaction of the user
        $userTransactions = Transactions::where('user_id', $user->id)->latest()->take(3)->get();
        $role = session('role'); 
        $savings = Savings::all();
        $members = User::count();  
        $transactions = Transactions::all();
        $loanCounts = Loans::selectRaw("
        COUNT(CASE WHEN status = 'disbursed' THEN 1 END) AS disbursed_loans,
        COUNT(CASE WHEN status = 'completed' THEN 1 END) AS completed_loans,
        COUNT(CASE WHEN status = 'overdue' THEN 1 END) AS defaulted_loans,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending_loans
        ")->first();    
        
        // dd($transactions);
        $totalContribution = 0;
        foreach ($transactions as $transaction) {
            $totalContribution = $totalContribution + $transaction->amount;
        }
        $totalSavings = 0;
        foreach ($savings as $saving) {
            $totalSavings += $saving->account_balance;
        }

        // dd($loans);
        // dd($totalSavings);
        $pending_loans = $loanCounts->pending_loans;
        $disbursed_loans = $loanCounts->disbursed_loans;
        $completed_loans = $loanCounts->completed_loans;
        $defaulted_loans = $loanCounts->defaulted_loans;
        $notifications = Auth::user()->notifications()->latest()->take(3)->get();
        // $config = Settings::all(); 
        return view('dashboard.dashboard', compact('user', 'role','members','pending_loans', 'disbursed_loans', 'completed_loans', 'defaulted_loans','totalContribution', 'totalSavings', 'notifications', 'loans', 'userTransactions'));
    }

    public function switchUser(Request $request){
        $user = Auth::user();
        // dd($request->all());
        if($request->role !== null){
            session(['role' => $request->role]);
            $role = session('role');
        }else{
            $role = session('role');
        }
        
        // return view('dashboard.dashboard', compact('user', 'role'));
        return redirect()->route('dashboard');
        // return view('dashboard.reset');
    }

}
