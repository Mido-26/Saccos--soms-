<?php

namespace App\Observers;

use App\Models\Loans;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\LoanRepaymentsController;

class LoanObserver
{
    /**
     * Handle the Loans "created" event.
     */
    public function created(Loans $loans): void
    {
        //
    }

    /**
     * Handle the Loans "updated" event.
     */
    public function updated(Loans $loans): void
{
    Log::info('Loan status updated', ['loan_id' => $loans->id, 'status' => $loans->status]);

    if ($loans->isDirty('status') && $loans->status === 'disbursed' && !$loans->disbursed_at) {
        Log::info('Loan status is disbursed, updating disbursed_at', ['loan_id' => $loans->id]);
        $loans->disbursed_at = now();
        $loans->outstanding_amount = $loans->loan_amount;
        $loans->saveQuietly();  // Save without triggering the observer
        
        (new LoanRepaymentsController())->createRepaymentSchedule($loans);
    }
}

    /**
     * Handle the Loans "deleted" event.
     */
    public function deleted(Loans $loans): void
    {
        //
    }

    /**
     * Handle the Loans "restored" event.
     */
    public function restored(Loans $loans): void
    {
        //
    }

    /**
     * Handle the Loans "force deleted" event.
     */
    public function forceDeleted(Loans $loans): void
    {
        //
    }
}
