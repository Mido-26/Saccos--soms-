<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Loans;
use App\Models\Penalts;
use App\Models\Savings;
use App\Models\Settings;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $loans = Loans::where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->first() ?? null;

        $userTransactions = Transactions::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

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
        
        // Updated contribution calculation
        $totalLoans = Loans::sum('loan_amount');
        $totalPenalties = Penalts::where('status', 'paid')->sum('amount');
        $totalContribution = $totalLoans + $totalPenalties;

        $totalSavings = Savings::sum('account_balance');

        // Chart data
        $data = [
            'savingsTrend' => $this->getSavingsTrend(),
            'membershipGrowth' => $this->getMembershipGrowth(),
            'contributions' => $this->getContributionsData()
        ];

        // dd($data);

        $pending_loans = $loanCounts->pending_loans;
        $disbursed_loans = $loanCounts->disbursed_loans;
        $completed_loans = $loanCounts->completed_loans;
        $defaulted_loans = $loanCounts->defaulted_loans;
        $notifications = Auth::user()->notifications()->latest()->take(3)->get();

        return view('dashboard.dashboard', compact(
            'user', 'role', 'members', 'pending_loans', 'disbursed_loans',
            'completed_loans', 'defaulted_loans', 'totalContribution',
            'totalSavings', 'notifications', 'loans', 'userTransactions', 'data'
        ));
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

    private function getContributionsData()
{
    $months = collect(now()->subMonths(6)->monthsUntil(now()))
        ->map(fn ($date) => $date->format('Y-m'))
        ->toArray(); // Convert to array

    // Get loan payments
    $loans = Loans::where('created_at', '>=', now()->subMonths(6))
        ->selectRaw('SUM(loan_amount) as total, DATE_FORMAT(created_at, "%Y-%m") as month')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    // Get penalties
    $penalties = Penalts::where('created_at', '>=', now()->subMonths(6))
        ->where('status', 'paid')
        ->selectRaw('SUM(amount) as total, DATE_FORMAT(created_at, "%Y-%m") as month')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    // Combine data with fallback for missing months
    $amounts = [];
    foreach ($months as $month) {
        $amounts[] = ($loans[$month] ?? 0) + ($penalties[$month] ?? 0);
    }

    return [
        'months' => array_map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'), $months),
        'amounts' => $amounts
    ];
}

// Update other chart data methods similarly:
private function getSavingsTrend()
{
    $data = Savings::where('created_at', '>=', now()->subMonths(6))
        ->selectRaw('SUM(account_balance) as total, DATE_FORMAT(created_at, "%Y-%m") as month')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    return [
        'months' => $data->pluck('month')->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))->toArray(),
        'amounts' => $data->pluck('total')->toArray()
    ];
}

private function getMembershipGrowth()
{
    $data = User::where('created_at', '>=', now()->subMonths(6))
        ->selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, "%Y-%m") as month')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    return [
        'months' => $data->pluck('month')->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))->toArray(),
        'counts' => $data->pluck('count')->toArray()
    ];
}
}
