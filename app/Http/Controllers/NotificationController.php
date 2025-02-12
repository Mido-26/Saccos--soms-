<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(10); // Query Builder + Pagination
        return view('notifications.index', compact('notifications'));
    }

    // Fetch unread notifications
    public function fetchNotifications()
    {
        $notifications = Auth::user()->unreadNotifications;
        return response()->json($notifications);
    }

    // Mark a notification as read
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    // Mark all notifications as read
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->back();
    }

    public function destroy()
{
    $user = Auth::user();

    // Delete all notifications for the user
    $deleted = $user->notifications()->delete(); 

    if ($deleted) {
        return redirect()->back()->with('success', 'Notifications deleted successfully');
    }
    
    return redirect()->back()->with('error', 'An error occurred while deleting notifications');
}

}
