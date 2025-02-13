<?php

use Carbon\Carbon;
// use Illuminate\Support\Facades\Schedule;
use App\Models\LoanRepayments;
use App\Jobs\OverdueInstallmentJob;
use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })->withSchedule(function(Schedule $schedule) {
        // Run the penalty check every day at midnight
        $schedule->command('penalties:check')->daily();

        $schedule->command('check:overdue-installments')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
