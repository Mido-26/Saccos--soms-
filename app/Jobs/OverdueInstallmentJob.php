<?php

namespace App\Jobs;

use App\Models\LoanRepayment;
use Illuminate\Bus\Queueable;
use App\Models\LoanRepayments;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\OverdueInstallmentNotification;  // If you are using notifications

class OverdueInstallmentJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public $installment;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\LoanRepayments  $installment
     * @return void
     */
    public function __construct(LoanRepayments $installment)
    {
        $this->installment = $installment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Process the overdue installment, such as notifying the user
        $installment = $this->installment;

        // Example: Send notification to the user if the installment is overdue
        $installment->user->notify(new OverdueInstallmentNotification($installment));

        // Optionally, update the loan repayment status
        $installment->status = 'overdue'; // Set the status to 'overdue' or any other appropriate status
        $installment->save(); // Save the updated status to the database
    }
}
