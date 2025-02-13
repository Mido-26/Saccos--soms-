<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Penalts;
use App\Models\Savings;
use Illuminate\Http\Request;
use App\Models\LoanRepayments;

class PenaltsController extends Controller
{
    /**
     * Check and apply penalties for missed savings.
     */
    public function checkSavingsPenalty()
    {
        $users = Savings::select('user_id')
            ->groupBy('user_id')
            ->get();

        foreach ($users as $user) {
            $lastSaving = Savings::where('user_id', $user->user_id)
                ->latest('last_deposit_date')
                ->first();

            if (!$lastSaving || Carbon::parse($lastSaving->date)->lt(Carbon::now()->subMonth())) {
                // Apply penalty
                Penalts::create([
                    'user_id' => $user->user_id,
                    'type' => 'savings',
                    'description' => 'Penalty for missing savings deposit.',
                    'amount' => 100, // Adjust as per penalty rules
                    'date' => Carbon::now(),
                    'status' => 'pending',
                ]);
            }
        }
    }

    /**
     * Check and apply penalties for overdue loan installments.
     */
    public function checkLoanPenalty()
    {
        $installments = LoanRepayments::where('due_date', '<', Carbon::now())
            ->where('status', 'pending')
            ->get();

        foreach ($installments as $installment) {
            // Apply penalty
            Penalts::create([
                'user_id' => $installment->user_id,
                'type' => 'loan',
                'description' => 'Penalty for overdue loan installment.',
                'amount' => $installment->amount * 0.1, // Example: 10% penalty
                'date' => Carbon::now(),
                'status' => 'pending',
            ]);
        }
    }
}

