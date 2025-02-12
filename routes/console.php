<?php
use Carbon\Carbon;
use App\Models\LoanRepayments;
use App\Jobs\OverdueInstallmentJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('schedule:run', function () {
    $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
    $schedule->call(function () {
        // Find overdue installments
        $installments = LoanRepayments::where('status', 'pending')
                                      ->where('due_date', '<', Carbon::now())
                                      ->get();

        // Trigger the event for overdue installments
        foreach ($installments as $installment) {
            event(new OverdueInstallmentJob($installment));
        }
    })->daily();
})->describe('Run the scheduled tasks');
