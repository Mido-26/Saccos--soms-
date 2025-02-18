<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Loans;
use Barryvdh\DomPDF\PDF;
// use Maatwebsite\Excel\Excel;
use Illuminate\Support\Str;
use App\Models\Transactions;
use App\Models\LoanRepayments;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Request;
use App\Notifications\LoanDisbursedNotification;
use App\Http\Requests\StoreLoanRepaymentsRequest;
use App\Http\Requests\UpdateLoanRepaymentsRequest;

class LoanRepaymentsController extends Controller
{
    public function show(Loans $loan)
    {
        // Get the currently authenticated user.
        $user = Auth::user();

        // Allow access if the user is the owner or if they have admin privileges.
        if ($loan->user_id !== $user->id && !$user->can('viewAdminDashboard')) {
            abort(403);
        }

        // Retrieve the installments (repayments) ordered by due date.
        $installments = $loan->repayments()
        ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'paid' THEN 1 ELSE 2 END")
        ->orderByRaw("CASE WHEN status = 'pending' THEN due_date ELSE NULL END ASC")
        ->orderByRaw("CASE WHEN status = 'paid' THEN due_date ELSE NULL END DESC")
        ->paginate(10);


        return view('loans.installments', [
            'loan' => $loan,
            'installments' => $installments,
        ]);
    }


    public function exportInstallments(Loans $loan, $type)
    {
        abort(503);
        $filename = "loan-{$loan->id}-installments-" . now()->format('Ymd-His') . ".$type";

        // return match ($type) {
        //     'excel' => Excel::download(new LoanInstallmentsExport($loan), $filename),
        //     'pdf' => PDF::loadView('exports.loan-installments', [
        //         'installments' => $loan->repayments,
        //         'loan' => $loan
        //     ])->download($filename),
        //     default => abort(404),
        // };
    }

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

     public function markAsPaid(LoanRepayments $repayment, Request $request)
{
    try {
        DB::transaction(function () use ($repayment) {
            $loan = $repayment->loan;

            // Check if there are unpaid previous installments
            $unpaidInstallments = LoanRepayments::where('loan_id', $loan->id)
                ->where('id', '<', $repayment->id) // Only check previous installments
                ->where('status', '!=', 'paid')
                ->exists();

            if ($unpaidInstallments) {
                throw new \Exception("Please ensure all previous installments are paid before processing this one.");
            }

            // Mark the current installment as paid
            $repayment->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);

            // Create a new transaction record
            Transactions::create([
                'user_id'               => $loan->user_id,
                'initiator_id'          => Auth::id(),
                'type'                  => 'loan_payment',
                'amount'                => $repayment->amount,
                'transaction_reference' => (string) Str::uuid(),
                'payment_method'        => 'cash',
                'description'           => "Payment for installment #{$repayment->id} of loan #{$loan->id}",
                'completed_at'          => now(),
                'metadata'              => json_encode([
                    'loan_id'      => $loan->id,
                    'repayment_id' => $repayment->id,
                    'due_date'     => $repayment->due_date,
                ]),
                'status'                => 'completed',
            ]);

            // Deduct the paid installment amount from the outstanding amount
            $loan->outstanding_amount -= $repayment->amount;

            // If the outstanding amount is zero or less, mark the loan as completed
            if ($loan->outstanding_amount <= 0) {
                $loan->outstanding_amount = 0;
                $loan->status = 'completed';
            }
            $loan->save();
        });

        return back()->with('success', 'Installment marked as paid successfully.');
    } catch (\Exception $e) {
        Log::error('Error marking installment as paid: ' . $e->getMessage());

        // Return a clean message for the user
        return back()->with('error', 'Unable to process the payment. Please ensure previous installments are paid first.');
    }
}

     


//     public function initiatePayment(LoanRepayments $repayment, Request $request)
// {
//     // Validate incoming data including the file upload.
//     $validated = $request->validate([
//         'payment_method'    => 'required|string|max:255',
//         'payment_reference' => 'nullable|string|max:255',
//         'payment_proof'     => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
//     ]);

//     // Check if the installment has already been paid.
//     if ($repayment->paid_at) {
//         return back()->with('error', 'This installment is already paid.');
//     }

//     try {
//         DB::transaction(function () use ($repayment, $validated, $request) {
//             // Store the payment proof file.
//             $filePath = null;
//             if ($request->hasFile('payment_proof')) {
//                 $file = $request->file('payment_proof');
//                 // This stores the file in the "payment_proofs" folder on the "public" disk.
//                 $filePath = $file->store('payment_proofs', 'public');
//             }

//             // Record the transaction, including the file path in the metadata.
//             Transactions::create([
//                 'user_id'               => $repayment->loan->user_id,
//                 'initiator_id'          => auth()->id(),
//                 'type'                  => 'loan_repayment',
//                 'amount'                => $repayment->amount,
//                 'transaction_reference' => $validated['payment_reference'] ?? (string) Str::uuid(),
//                 'payment_method'        => $validated['payment_method'],
//                 'description'           => "Payment for installment #{$repayment->id} of loan #{$repayment->loan_id}",
//                 'completed_at'          => now(),
//                 'metadata'              => [
//                     'loan_id'       => $repayment->loan_id,
//                     'repayment_id'  => $repayment->id,
//                     'due_date'      => $repayment->due_date,
//                     'payment_proof' => $filePath,
//                 ],
//             ]);
//         });

//         return back()->with('success', 
//             "Installment #{$repayment->id} for {$repayment->amount} has been added successfully. Waiting for Admin Approval."
//         );
//     } catch (\Exception $e) {
//         DB::rollBack();
//         Log::error('Payment initiation failed: ' . $e->getMessage());
//         return back()->with('error', 'Failed to initiate payment. Please try again.');
//     }
// }

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
