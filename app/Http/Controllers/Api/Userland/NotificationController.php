<?php

namespace App\Http\Controllers\Api\Userland;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Userland\NotificationResource;
use App\Models\Notification;


class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $notifications = Notification::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(20);
        $notifications->load(['user', 'notifiable']);
        return NotificationResource::collection($notifications);
    }

    public function markAsRead(Notification $notification)
    {
        $user = auth()->user();
        if ($notification->user_id !== $user->id)
            return response()->json(['message' => 'You are not authorized to perform this action'], 403);

        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'Notification marked as read'], 200);
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        Notification::where('user_id', $user->id)->update(['is_read' => true]);
        return response()->json(['message' => 'All notifications marked as read'], 200);
    }

}
