<?php

namespace App\Observers;

use App\Models\Loans;
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
        // / Check if status changed to "disbursed" and hasn't been disbursed before
        if ($loans->isDirty('status') && $loans->status === 'disbursed' && !$loans->disbursed_at) {
            $loans->disbursed_at = now();
            $loans->outstanding_amount = $loans->loan_amount;
            $loans->saveQuietly(); // Save without re-triggering the observer
            
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
