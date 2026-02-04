<?php

/**
 * Admin notification endpoints for listing and managing in-app alerts.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Provides APIs for viewing, marking, and deleting admin notifications.
 */
class AdminNotificationController extends Controller
{
    /**
     * Enforce admin authentication for notification management.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display the notifications index page.
     */
    public function index()
    {
        $user = auth()->user();
        $notifications = $this->baseQuery($user)
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Return the latest notifications and unread count for the admin panel.
     */
    public function panel(Request $request)
    {
        $user = auth()->user();
        $query = $this->baseQuery($user)->latest();

        $notifications = $query->take(10)->get();
        $unreadCount = (clone $query)->where('is_read', false)->count();

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

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        $this->authorizeNotification($notification);
        $notification->markAsRead();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Mark all notifications as read for the current admin.
     */
    public function markAllAsRead()
    {
        $user = auth()->user();
        $this->baseQuery($user)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Delete a notification after authorization.
     */
    public function destroy(Notification $notification)
    {
        $this->authorizeNotification($notification);
        $notification->delete();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Ensure the notification belongs to the current admin.
     */
    protected function authorizeNotification(Notification $notification): void
    {
        $user = auth()->user();
        if ($notification->notifiable_id !== $user->id || $notification->notifiable_type !== User::class) {
            abort(403);
        }
    }

    /**
     * Base query for admin-visible notifications.
     */
    protected function baseQuery(User $user)
    {
        return Notification::where('notifiable_id', $user->id)
            ->where('notifiable_type', User::class)
            ->where(function ($q) {
                $q->whereNull('data->audience')
                  ->orWhere('data->audience', 'admin');
            });
    }
}
