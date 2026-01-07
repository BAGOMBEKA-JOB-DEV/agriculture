<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get latest 20 notifications
        $notifications = $user->notifications()->latest()->limit(20)->get();
        
        return response()->json($notifications);
    }

    public function show($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        
        // If it's a SystemAlert, we might want to attach the full Announcement
        if ($notification->type === 'App\Notifications\SystemAlert') {
             $notification->data = array_merge($notification->data, [
                 'announcement' => \App\Models\Announcement::find($notification->data['announcement_id'])
             ]);
        }
        
        return response()->json($notification);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
