@extends('products.layout')

@section('content')

<div class="container py-4">

    {{-- =========================================================
        Header
    ========================================================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                🔔 Product Event Notifications
            </h2>

            <p class="text-muted mb-0">
                Monitor product model events and notifications
            </p>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('products.index') }}"
               class="btn btn-outline-primary">
                ← Products
            </a>

            @if($unreadCount > 0)
                <form method="POST"
                      action="{{ route('notifications.mark-all-read') }}">
                    @csrf

                    <button type="submit"
                            class="btn btn-success">
                        ✓ Mark All Read
                    </button>
                </form>
            @endif

            <form method="POST"
                  action="{{ route('notifications.clear') }}"
                  onsubmit="return confirm('Are you sure you want to clear all notifications?');">

                @csrf
                @method('DELETE')

                <button type="submit"
                        class="btn btn-outline-danger">
                    🗑 Clear All
                </button>

            </form>

        </div>

    </div>


    {{-- =========================================================
        Statistics
    ========================================================== --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <div class="text-muted">
                        Total Notifications
                    </div>

                    <h2 class="fw-bold mb-0">
                        {{ $notifications->total() }}
                    </h2>

                </div>
            </div>

        </div>


        <div class="col-md-4">

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <div class="text-muted">
                        Unread Notifications
                    </div>

                    <h2 class="fw-bold text-danger mb-0">
                        {{ $unreadCount }}
                    </h2>

                </div>
            </div>

        </div>


        <div class="col-md-4">

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <div class="text-muted">
                        Event Types
                    </div>

                    <h2 class="fw-bold mb-0">
                        {{ $eventCounts->count() }}
                    </h2>

                </div>
            </div>

        </div>

    </div>


    {{-- =========================================================
        Search / Filter
    ========================================================== --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white">

            <h5 class="mb-0 fw-bold">
                🔎 Search & Filter Notifications
            </h5>

        </div>


        <div class="card-body">

            {{-- IMPORTANT:
                 Route name is notifications.index
            --}}

            <form method="GET"
                  action="{{ route('notifications.index') }}"
                  class="row g-3">

                {{-- Search --}}
                <div class="col-md-5">

                    <label class="form-label fw-semibold">
                        Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        class="form-control"
                        placeholder="Search title, message or event..."
                    >

                </div>


                {{-- Event --}}
                <div class="col-md-3">

                    <label class="form-label fw-semibold">
                        Event Type
                    </label>

                    <select
                        name="event"
                        class="form-select">

                        <option value="">
                            All Events
                        </option>

                        @foreach($events as $eventName)

                            <option
                                value="{{ $eventName }}"
                                {{ ($event ?? '') == $eventName ? 'selected' : '' }}>

                                {{ ucfirst($eventName) }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Read Status --}}
                <div class="col-md-2">

                    <label class="form-label fw-semibold">
                        Status
                    </label>

                    <select
                        name="is_read"
                        class="form-select">

                        <option value="">
                            All
                        </option>

                        <option
                            value="0"
                            {{ isset($isRead) && $isRead === '0' ? 'selected' : '' }}>
                            Unread
                        </option>

                        <option
                            value="1"
                            {{ isset($isRead) && $isRead === '1' ? 'selected' : '' }}>
                            Read
                        </option>

                    </select>

                </div>


                {{-- Submit --}}
                <div class="col-md-2 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        🔍 Filter

                    </button>

                </div>

            </form>


            {{-- Clear Filters --}}
            @if(
                !empty($search) ||
                !empty($event) ||
                ($isRead ?? '') !== ''
            )

                <div class="mt-3">

                    <a href="{{ route('notifications.index') }}"
                       class="btn btn-sm btn-outline-secondary">

                        ✕ Clear Filters

                    </a>

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
        Notifications
    ========================================================== --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-bold">
                    Notifications
                </h5>

                <span class="badge bg-primary">
                    {{ $notifications->total() }}
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            @if($notifications->count() > 0)

                <div class="list-group list-group-flush">

                    @foreach($notifications as $notification)

                        <div
                            class="list-group-item
                            {{ !$notification->is_read ? 'bg-light' : '' }}">

                            <div class="d-flex justify-content-between">

                                {{-- Left --}}
                                <div class="d-flex gap-3">

                                    {{-- Icon --}}
                                    <div>

                                        <span
                                            class="badge
                                            bg-{{ $notification->event_badge }}
                                            rounded-circle p-3">

                                            {{ $notification->event_icon }}

                                        </span>

                                    </div>


                                    {{-- Content --}}
                                    <div>

                                        <div class="d-flex align-items-center gap-2">

                                            <h6 class="mb-1 fw-bold">

                                                {{ $notification->title }}

                                            </h6>


                                            @if(!$notification->is_read)

                                                <span class="badge bg-danger">
                                                    New
                                                </span>

                                            @else

                                                <span class="badge bg-secondary">
                                                    Read
                                                </span>

                                            @endif

                                        </div>


                                        <p class="mb-1 text-muted">

                                            {{ $notification->message }}

                                        </p>


                                        <div class="small text-muted">

                                            <span class="badge
                                                bg-{{ $notification->event_badge }}">

                                                {{ ucfirst($notification->event) }}

                                            </span>


                                            @if($notification->product)

                                                <span class="ms-2">

                                                    Product:
                                                    <strong>
                                                        #{{ $notification->product->id }}
                                                        -
                                                        {{ $notification->product->name }}
                                                    </strong>

                                                </span>

                                            @endif


                                            <span class="ms-2">

                                                {{ $notification->created_at->format('d M Y, h:i A') }}

                                            </span>

                                        </div>

                                    </div>

                                </div>


                                {{-- Right --}}
                                <div class="d-flex align-items-center gap-2">

                                    @if(!$notification->is_read)

                                        <form
                                            method="POST"
                                            action="{{ route('notifications.read', $notification) }}">

                                            @csrf

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-success"
                                                title="Mark as read">

                                                ✓

                                            </button>

                                        </form>

                                    @endif


                                    <form
                                        method="POST"
                                        action="{{ route('notifications.destroy', $notification) }}"
                                        onsubmit="return confirm('Delete this notification?');">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete">

                                            🗑

                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="text-center py-5">

                    <div class="fs-1 mb-3">
                        🔔
                    </div>

                    <h5>
                        No notifications found
                    </h5>

                    <p class="text-muted mb-0">

                        There are no notifications matching your filters.

                    </p>

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
        Numeric Pagination
    ========================================================== --}}
    @if($notifications->hasPages())

        <div class="d-flex justify-content-center mt-4">

            <nav aria-label="Notification pagination">

                <ul class="pagination">

                    @for(
                        $page = 1;
                        $page <= $notifications->lastPage();
                        $page++
                    )

                        <li
                            class="page-item
                            {{ $notifications->currentPage() == $page ? 'active' : '' }}">

                            <a
                                class="page-link"
                                href="{{ request()->fullUrlWithQuery(['page' => $page]) }}">

                                {{ $page }}

                            </a>

                        </li>

                    @endfor

                </ul>

            </nav>

        </div>

    @endif


</div>

@endsection