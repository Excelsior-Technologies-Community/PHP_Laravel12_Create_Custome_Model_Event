<?php

namespace App\Http\Controllers;

use App\Models\ProductEventNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductEventNotificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Notification List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $search = $request->get('search');

        $event = $request->get('event');

        $isRead = $request->get('is_read');


        /*
        |--------------------------------------------------------------------------
        | Notifications Query
        |--------------------------------------------------------------------------
        */

        $notifications = ProductEventNotification::with('product')

            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where(
                        'title',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'message',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'event',
                        'like',
                        "%{$search}%"
                    );

                });

            })


            ->when($event, function ($query) use ($event) {

                $query->where(
                    'event',
                    $event
                );

            })


            ->when(
                $isRead !== null &&
                $isRead !== '',
                function ($query) use ($isRead) {

                    $query->where(
                        'is_read',
                        (int) $isRead
                    );

                }
            )


            ->latest()

            ->paginate(15)

            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Unread Count
        |--------------------------------------------------------------------------
        */

        $unreadCount = ProductEventNotification::where(
            'is_read',
            false
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Event Statistics
        |--------------------------------------------------------------------------
        */

        $eventCounts = ProductEventNotification::select(
            'event',
            DB::raw('COUNT(*) as total')
        )

        ->groupBy('event')

        ->orderByDesc('total')

        ->get();


        /*
        |--------------------------------------------------------------------------
        | Event Dropdown
        |--------------------------------------------------------------------------
        */

        $events = ProductEventNotification::select('event')

            ->distinct()

            ->orderBy('event')

            ->pluck('event');


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'products.notifications',
            compact(
                'notifications',
                'unreadCount',
                'eventCounts',
                'events',
                'search',
                'event',
                'isRead'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Latest Notifications
    |--------------------------------------------------------------------------
    */

    public function latest()
    {
        $notifications = ProductEventNotification::with('product')

            ->latest()

            ->take(10)

            ->get();


        $unreadCount = ProductEventNotification::where(
            'is_read',
            false
        )->count();


        return response()->json([

            'notifications' => $notifications,

            'unread_count' => $unreadCount,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Mark Notification As Read
    |--------------------------------------------------------------------------
    */

    public function markAsRead(
        ProductEventNotification $notification
    ) {

        $notification->update([

            'is_read' => true,

        ]);


        return redirect()

            ->route('notifications.index')

            ->with(
                'success',
                'Notification marked as read.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Mark All Notifications As Read
    |--------------------------------------------------------------------------
    */

    public function markAllAsRead()
    {
        ProductEventNotification::where(
            'is_read',
            false
        )

        ->update([

            'is_read' => true,

        ]);


        return redirect()

            ->route('notifications.index')

            ->with(
                'success',
                'All notifications marked as read.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Notification
    |--------------------------------------------------------------------------
    */

    public function destroy(
        ProductEventNotification $notification
    ) {

        $notification->delete();


        return redirect()

            ->route('notifications.index')

            ->with(
                'success',
                'Notification deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Clear All Notifications
    |--------------------------------------------------------------------------
    */

    public function clear()
    {
        ProductEventNotification::query()->delete();


        return redirect()

            ->route('notifications.index')

            ->with(
                'success',
                'All notifications cleared successfully.'
            );
    }
}