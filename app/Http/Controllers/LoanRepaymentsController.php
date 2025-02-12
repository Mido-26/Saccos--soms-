<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Loans;
use App\Models\LoanRepayments;
use App\Http\Requests\StoreLoanRepaymentsRequest;
use App\Http\Requests\UpdateLoanRepaymentsRequest;

class LoanRepaymentsController extends Controller
{
    
    private function createRepaymentSchedule(Loans $loan)
    {
        $installmentAmount = $loan->amount / $loan->tenure_months; // Monthly payment
        $dueDate = now()->addMonth(); // First payment in one month
    
        for ($i = 1; $i <= $loan->tenure_months; $i++) {
            LoanRepayments::create([
                'loan_id' => $loan->id,
                'due_date' => $dueDate,
                'amount' => $installmentAmount,
                'status' => 'pending',
            ]);
    
            $dueDate = $dueDate->addMonth(); // Set next installment date
        }
    }

        public function markAsPaid(LoanRepayments $repayment)
    {
        $repayment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Check if all installments are paid, then mark loan as completed
        $loan = $repayment->loan;
        if ($loan->repayments()->where('status', 'pending')->count() == 0) {
            $loan->update(['status' => 'completed']);
        }

        return back()->with('success', 'Installment marked as paid.');
    }


        public function markOverdueInstallments()
    {
        // Get all unpaid installments
        $installments = LoanRepayments::where('status', 'pending')
                                    ->where('due_date', '<', Carbon::now()->subDays(5)) // Overdue condition (due date + grace period)
                                    ->get();

        foreach ($installments as $installment) {
            // Mark as overdue
            $installment->status = 'overdue';
            $installment->save();
        }
    }

        public function markLoanAsCompleted($loanId)
    {
        $loan = Loans::findOrFail($loanId);
        
        // Check if all installments are paid
        $allPaid = LoanRepayments::where('loan_id', $loanId)
                                ->where('status', 'paid')
                                ->count() == $loan->loan_duration; // Loan duration is the total number of installments
        
        if ($allPaid) {
            // Mark loan as completed
            $loan->status = 'completed';
            $loan->save();
        }
    }
    
}
