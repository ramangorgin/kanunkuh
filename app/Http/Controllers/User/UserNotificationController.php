<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $user = auth()->user();
        $notifications = $this->baseQuery($user)
            ->latest()
            ->paginate(20);

        return view('user.notifications.index', compact('notifications'));
    }

    public function panel(Request $request)
    {
        $user = auth()->user();
        $query = $this->baseQuery($user)->latest();

        $notifications = $query->take(10)->get();
        $unreadCount = (clone $query)->where('is_read', false)->count();

        // Auto mark as read if requested (when dropdown opened)
        if ($request->boolean('mark')) {
            $this->baseQuery($user)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        }

        return response()->json([
            'unread_count' => $unreadCount,
            'items' => $notifications->map(function (Notification $n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'is_read' => $n->is_read,
                    'created_at' => optional($n->created_at)->diffForHumans(),
                ];
            }),
        ]);
    }

    public function markAsRead(Notification $notification)
    {
        $this->authorizeNotification($notification);
        $notification->markAsRead();

        return response()->json(['status' => 'ok']);
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        $this->baseQuery($user)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['status' => 'ok']);
    }

    public function destroy(Notification $notification)
    {
        $this->authorizeNotification($notification);
        $notification->delete();

        return response()->json(['status' => 'ok']);
    }

    protected function authorizeNotification(Notification $notification): void
    {
        $user = auth()->user();
        if ($notification->notifiable_id !== $user->id || $notification->notifiable_type !== User::class) {
            abort(403);
        }
    }

    protected function baseQuery(User $user)
    {
        return Notification::where('notifiable_id', $user->id)
            ->where('notifiable_type', User::class)
            ->where(function ($q) {
                $q->whereNull('data->audience')
                  ->orWhere('data->audience', 'user');
            });
    }
}
