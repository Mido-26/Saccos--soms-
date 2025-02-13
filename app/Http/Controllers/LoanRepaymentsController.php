<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Loans;
use App\Models\LoanRepayments;
use App\Notifications\LoanDisbursedNotification;
use App\Http\Requests\StoreLoanRepaymentsRequest;
use App\Http\Requests\UpdateLoanRepaymentsRequest;

class LoanRepaymentsController extends Controller
{
    /**
     * Create a repayment schedule for the given loan.
     */
    public function createRepaymentSchedule(Loans $loan)
    {
        $installmentAmount = $loan->monthly_payments; // Monthly payment amount
        $dueDate = now()->addMonth(); // First payment is due in one month

        // Create installment records for each month in the loan duration.
        for ($i = 1; $i <= $loan->loan_duration; $i++) {
            LoanRepayments::create([
                'loan_id'  => $loan->id,
                // Use a copy of the due date so that the original isn't mutated.
                'due_date' => $dueDate->copy(),
                'amount'   => $installmentAmount,
                'status'   => 'pending',
            ]);

            $dueDate->addMonth(); // Set next installment date
        }

        // If the loan does not have an outstanding amount set, initialize it.
        if (!$loan->outstanding_amount) {
            $loan->update(['outstanding_amount' => $loan->loan_amount]);
        }

        // Notify the user that the loan has been disbursed.
        $loan->user->notify(new LoanDisbursedNotification($loan));
    }

    /**
     * Mark an installment as paid and update the loan's outstanding amount.
     */
    public function markAsPaid(LoanRepayments $repayment)
    {
        // Mark the repayment installment as paid.
        $repayment->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        $loan = $repayment->loan;

        // Deduct the paid installment amount from the outstanding amount.
        $loan->outstanding_amount -= $repayment->amount;

        // If the outstanding amount is zero or less, mark the loan as completed.
        if ($loan->outstanding_amount <= 0) {
            $loan->outstanding_amount = 0;
            $loan->status = 'completed';
        }
        $loan->save();

        return back()->with('success', 'Installment marked as paid.');
    }

    /**
     * Mark overdue installments.
     */
    public function markOverdueInstallments()
    {
        // Get all pending installments that are overdue (due date + 5-day grace period)
        $installments = LoanRepayments::where('status', 'pending')
            ->where('due_date', '<', Carbon::now())
            ->get();

        foreach ($installments as $installment) {
            $installment->update([
                'status' => 'overdue'
            ]);
        }
    }

    /**
     * Mark a loan as completed if all installments are paid.
     */
    public function markLoanAsCompleted($loanId)
    {
        $loan = Loans::findOrFail($loanId);

        // Calculate total paid amount for this loan.
        $paidAmount = $loan->repayments()->where('status', 'paid')->sum('amount');

        // If the paid amount is equal to or exceeds the loan's principal, mark the loan as completed.
        if ($paidAmount >= $loan->loan_amount) {
            $loan->update([
                'status'             => 'completed',
                'outstanding_amount' => 0,
            ]);
        }
    }
}
