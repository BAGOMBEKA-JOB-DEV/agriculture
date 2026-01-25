<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Announcement;
use App\Notifications\SystemAlert;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        return Announcement::with('creator')->latest()->paginate(10);
    }

    public function agriPulseNotifications(Request $request)
    {
        $query = \Illuminate\Support\Facades\DB::table('notifications')
            ->join('users', 'notifications.notifiable_id', '=', 'users.id')
            ->where('notifications.type', 'App\Notifications\AgriPulse\ModerationNotification')
            ->where('notifications.notifiable_type', 'App\Models\User');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('notifications.data', 'like', "%{$search}%");
            });
        }

        if ($request->filled('priority')) {
            $query->where('notifications.data', 'like', '%"priority":"' . $request->priority . '"%');
        }

        $notifications = $query->select('notifications.*', 'users.name as target_user_name', 'users.email as target_user_email')
            ->latest('notifications.created_at')
            ->paginate(15);

        // Parse JSON data for frontend consumption
        $notifications->getCollection()->transform(function ($n) {
            $n->data = json_decode($n->data, true);
            return $n;
        });

        return $notifications;
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:normal,high,critical'
        ]);

        $announcement = Announcement::create([
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority,
            'created_by' => auth()->id()
        ]);

        // Broadcast to all users
        User::chunk(100, function ($users) use ($announcement) {
            Notification::send($users, new SystemAlert($announcement));
        });

        return response()->json([
            'message' => 'Announcement broadcasted successfully.',
            'announcement' => $announcement
        ]);
    }

    public function show($id)
    {
        return Announcement::with('creator')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        
        $request->validate([
            'subject' => 'string|max:255',
            'message' => 'string',
            'priority' => 'in:normal,high,critical'
        ]);

        $announcement->update($request->only(['subject', 'message', 'priority']));

        return response()->json([
            'message' => 'Announcement updated successfully.',
            'announcement' => $announcement
        ]);
    }
}
