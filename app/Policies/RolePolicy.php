<?php

namespace App\Policies;

use session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the dashboard.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response
     */
    public function viewAdminDashboard(): bool
    {
        // $role = session('role');
        $role = session('role');
        // dd($role);
        // Check if the user has a valid role (adjust according to your application's roles)
        return in_array($role, ['admin', 'staff', 'superadmin']);
    }

    public function switchRole(): bool
    {
        $user = Auth::user();
        // dd($user);
        return in_array($user->role, ['admin', 'staff', 'superadmin']);
    }
}
