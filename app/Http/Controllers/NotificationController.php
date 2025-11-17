<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request)
    {
        $query = auth()->user()->notifications()->with('notifiable');

        // Apply filters
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->today();
                    break;
                case 'week':
                    $query->thisWeek();
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
            }
        }

        $notifications = $query->recent()->paginate(20);

        // Get statistics
        $stats = $this->getNotificationStats();

        return view('notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Get unread notifications count (for AJAX/API).
     */
    public function getUnreadCount()
    {
        $count = auth()->user()->notifications()->unread()->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Get recent notifications (for dropdown/bell icon).
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 10);
        $includeRead = $request->boolean('include_read', false);
        
        $query = auth()->user()->notifications()->with('notifiable');
        
        if (!$includeRead) {
            $query->unread();
        }
        
        $notifications = $query->recent()
            ->take($limit)
            ->get()
            ->map(function ($notification) {
                return $notification->toSummaryArray();
            });

        $unreadCount = auth()->user()->notifications()->unread()->count();
        $urgentCount = auth()->user()->notifications()->unread()->where('priority', Notification::PRIORITY_URGENT)->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'urgent_count' => $urgentCount,
            'has_notifications' => $notifications->isNotEmpty()
        ]);
    }

    /**
     * Mark a single notification as read and optionally redirect.
     */
    public function markAsRead($id, Request $request)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $url = $notification->getUrl();
        $notification->markAsRead();

        if ($request->boolean('redirect', false)) {
            return redirect($url);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'redirect_url' => $url
            ]);
        }

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        auth()->user()->notifications()->unread()->update(['read_at' => now()]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete a single notification.
     */
    public function destroy(Notification $notification)
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            abort(403);
        }

        $notification->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification deleted'
            ]);
        }

        return redirect()->back()->with('success', 'Notification deleted');
    }

    /**
     * Delete all read notifications.
     */
    public function deleteAllRead()
    {
        auth()->user()->notifications()->read()->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'All read notifications deleted'
            ]);
        }

        return redirect()->back()->with('success', 'All read notifications deleted');
    }

    /**
     * Delete all notifications.
     */
    public function deleteAll()
    {
        auth()->user()->notifications()->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'All notifications deleted'
            ]);
        }

        return redirect()->back()->with('success', 'All notifications deleted');
    }

    /**
     * Get notification statistics.
     */
    public function getNotificationStats()
    {
        $user = auth()->user();
        
        return [
            'total' => $user->notifications()->count(),
            'unread' => $user->notifications()->unread()->count(),
            'urgent' => $user->notifications()->where('priority', Notification::PRIORITY_URGENT)->count(),
            'today' => $user->notifications()->today()->count(),
            'this_week' => $user->notifications()->thisWeek()->count(),
            'by_type' => $user->notifications()
                ->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'by_category' => $user->notifications()
                ->selectRaw('category, count(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray(),
        ];
    }

    /**
     * Get statistics API endpoint.
     */
    public function stats()
    {
        $stats = $this->getNotificationStats();

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Mark notifications as read by type.
     */
    public function markTypeAsRead(Request $request)
    {
        $request->validate([
            'type' => 'required|string'
        ]);

        $count = auth()->user()
            ->notifications()
            ->unread()
            ->where('type', $request->type)
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => "Marked {$count} notifications as read",
            'count' => $count
        ]);
    }

    /**
     * Delete old notifications.
     */
    public function deleteOld(Request $request)
    {
        $days = $request->get('days', 30);
        
        $count = auth()->user()
            ->notifications()
            ->olderThan($days)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$count} old notifications",
            'count' => $count
        ]);
    }

    /**
     * Bulk mark notifications as read.
     */
    public function bulkMarkAsRead(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        $count = auth()->user()
            ->notifications()
            ->whereIn('id', $request->ids)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => "Marked {$count} notifications as read",
            'count' => $count
        ]);
    }

    /**
     * Bulk delete notifications.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        $count = auth()->user()
            ->notifications()
            ->whereIn('id', $request->ids)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$count} notifications",
            'count' => $count
        ]);
    }
}

