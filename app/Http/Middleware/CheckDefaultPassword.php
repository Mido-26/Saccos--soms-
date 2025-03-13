<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CheckDefaultPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure the user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Check if the password is still the default 'password'
            if (Hash::check('password', $user->password)) {
                return redirect()->route('password.change')->with('warning', 'Please change your default password.');
            }
        }

        return $next($request);
    }
}
