<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Loans;
use App\Models\Settings;
use App\Policies\RolePolicy;
use App\Observers\LoanObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Loans::observe(LoanObserver::class);
        function generateProfileAcronym($firstName, $secondName)
    {
        $firstLetter = strtoupper(substr($firstName, 0, 1));
        $secondLetter = strtoupper(substr($secondName, 0, 1));

        return $firstLetter . $secondLetter;
    }
        Gate::define('viewAdminDashboard', [RolePolicy::class, 'viewAdminDashboard']);
        Event::listen(
            Registered::class,
            SendEmailVerificationNotification::class
        );
        $settings = Settings::first();
        View::share('settings' , $settings);

        // Share the initials variable with all views
        view()->composer('*', function ($view) {
            
            if (Auth::check()) {
                $fName = Auth::user()->first_name;
                $lName = Auth::user()->last_name;
                $initials = generateProfileAcronym($fName, $lName);
                $view->with('initials', $initials);
            }
        });
    }
}
