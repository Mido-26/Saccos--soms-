<?php
namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\LoanRepayments;
use Illuminate\Console\Command;
use App\Jobs\OverdueInstallmentJob;

class CheckOverdueInstallments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:overdue-installments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue loan installments and trigger events for overdue installments.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Find overdue installments
        $installments = LoanRepayments::where('status', 'pending')
                                      ->where('due_date', '<', Carbon::now())
                                      ->get();

        // Trigger the event for overdue installments
        foreach ($installments as $installment) {
            event(new OverdueInstallmentJob($installment));
        }

        $this->info('Overdue installments checked and events triggered.');
    }
}
