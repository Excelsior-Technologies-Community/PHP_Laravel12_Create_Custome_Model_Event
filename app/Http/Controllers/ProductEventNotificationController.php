<?php

namespace App\Http\Controllers;

use App\Models\ProductEventNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductEventNotificationController extends Controller
{
    /**
     * Display notification center.
     */
    public function index(Request $request): View
    {
        $event = $request->get('event');

        $notifications = ProductEventNotification::with('product')
            ->when($event, function ($query) use ($event) {
                $query->where('event', $event);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $unreadCount = ProductEventNotification::where('is_read', false)->count();

        $eventCounts = ProductEventNotification::query()
            ->selectRaw('event, COUNT(*) as total')
            ->groupBy('event')
            ->pluck('total', 'event');

        return view('products.notifications', compact(
            'notifications',
            'unreadCount',
            'eventCounts',
            'event'
        ));
    }

    /**
     * Return latest notifications for AJAX polling.
     */
    public function latest(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 10), 50);

        $notifications = ProductEventNotification::with('product')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (ProductEventNotification $notification) {
                return [
                    'id' => $notification->id,
                    'product_id' => $notification->product_id,
                    'product_name' => $notification->product?->name ?? 'Deleted Product',
                    'event' => $notification->event,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'badge' => $notification->event_badge,
                    'icon' => $notification->event_icon,
                    'created_at' => $notification->created_at?->diffForHumans(),
                ];
            });

        $unreadCount = ProductEventNotification::where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark one notification as read.
     */
    public function markAsRead(
        ProductEventNotification $notification
    ): JsonResponse {
        $notification->update([
            'is_read' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
            'unread_count' => ProductEventNotification::where(
                'is_read',
                false
            )->count(),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        ProductEventNotification::where('is_read', false)
            ->update([
                'is_read' => true,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
            'unread_count' => 0,
        ]);
    }

    /**
     * Delete one notification.
     */
    public function destroy(
        ProductEventNotification $notification
    ): JsonResponse {
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted.',
            'unread_count' => ProductEventNotification::where(
                'is_read',
                false
            )->count(),
        ]);
    }

    /**
     * Clear all notifications.
     */
    public function clearAll(): JsonResponse
    {
        ProductEventNotification::query()->delete();

        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared.',
            'unread_count' => 0,
        ]);
    }
}