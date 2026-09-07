@extends('products.layout')

@section('content')

<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-speedometer2"></i>
                Product Event Dashboard
            </h2>
            <p class="text-muted mb-0">
                Monitor product status and model events
            </p>
        </div>

        <a href="{{ route('products.index') }}" class="btn btn-primary">
            <i class="bi bi-box-seam"></i>
            Products
        </a>
    </div>


    {{-- Date Filter --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-dark text-white">
            <i class="bi bi-calendar3"></i>
            Filter Events by Date
        </div>

        <div class="card-body">

            <form method="GET"
                action="{{ route('products.dashboard') }}"
                class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        From Date
                    </label>

                    <input
                        type="date"
                        name="from"
                        class="form-control"
                        value="{{ request('from') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        To Date
                    </label>

                    <input
                        type="date"
                        name="to"
                        class="form-control"
                        value="{{ request('to') }}">
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i>
                        Apply Filter
                    </button>

                    <a href="{{ route('products.dashboard') }}"
                        class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                        Clear
                    </a>

                </div>

            </form>

        </div>
    </div>


    {{-- Main Statistics --}}
    <div class="row g-4 mb-4">

        {{-- Total Products --}}
        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">

                    <div class="fs-2 text-primary">
                        <i class="bi bi-box"></i>
                    </div>

                    <h3 class="fw-bold">
                        {{ $totalProducts }}
                    </h3>

                    <p class="text-muted mb-0">
                        Total Products
                    </p>

                </div>
            </div>
        </div>


        {{-- Active --}}
        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">

                    <div class="fs-2 text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <h3 class="fw-bold">
                        {{ $activeProducts }}
                    </h3>

                    <p class="text-muted mb-0">
                        Active
                    </p>

                </div>
            </div>
        </div>


        {{-- Inactive --}}
        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">

                    <div class="fs-2 text-secondary">
                        <i class="bi bi-dash-circle"></i>
                    </div>

                    <h3 class="fw-bold">
                        {{ $inactiveProducts }}
                    </h3>

                    <p class="text-muted mb-0">
                        Inactive
                    </p>

                </div>
            </div>
        </div>


        {{-- Deactivated --}}
        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">

                    <div class="fs-2 text-warning">
                        <i class="bi bi-pause-circle"></i>
                    </div>

                    <h3 class="fw-bold">
                        {{ $deactivatedProducts }}
                    </h3>

                    <p class="text-muted mb-0">
                        Deactivated
                    </p>

                </div>
            </div>
        </div>


        {{-- Archived --}}
        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">

                    <div class="fs-2 text-danger">
                        <i class="bi bi-archive"></i>
                    </div>

                    <h3 class="fw-bold">
                        {{ $archivedProducts }}
                    </h3>

                    <p class="text-muted mb-0">
                        Archived
                    </p>

                </div>
            </div>
        </div>


        {{-- Total Events --}}
        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">

                    <div class="fs-2 text-info">
                        <i class="bi bi-lightning-charge"></i>
                    </div>

                    <h3 class="fw-bold">
                        {{ $totalEvents }}
                    </h3>

                    <p class="text-muted mb-0">
                        Events
                    </p>

                </div>
            </div>
        </div>

    </div>


    {{-- Event Statistics --}}
    <div class="row g-4 mb-4">

        <div class="col-lg-5">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-primary text-white">
                    <i class="bi bi-bar-chart"></i>
                    Event Statistics
                </div>

                <div class="card-body">

                    @forelse($eventCounts as $event => $count)

                    <div class="d-flex justify-content-between
                                    align-items-center
                                    border-bottom
                                    py-3">

                        <div>
                            @php
                            $badge = match($event) {
                            'activated' => 'success',
                            'deactivated' => 'warning',
                            'archived' => 'danger',
                            'statusChanged' => 'primary',
                            'priceChanged' => 'info',
                            default => 'secondary',
                            };
                            @endphp

                            <span class="badge bg-{{ $badge }}">
                                {{ $event }}
                            </span>
                        </div>

                        <strong class="fs-5">
                            {{ $count }}
                        </strong>

                    </div>

                    @empty

                    <div class="text-center text-muted py-4">
                        <i class="bi bi-info-circle fs-3"></i>

                        <p class="mb-0 mt-2">
                            No events found for the selected date range.
                        </p>
                    </div>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- Current Status Summary --}}
        <div class="col-lg-7">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-dark text-white">
                    <i class="bi bi-pie-chart"></i>
                    Current Product Status
                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between">
                                    <span>
                                        <i class="bi bi-check-circle text-success"></i>
                                        Active
                                    </span>

                                    <strong>
                                        {{ $activeProducts }}
                                    </strong>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between">
                                    <span>
                                        <i class="bi bi-dash-circle text-secondary"></i>
                                        Inactive
                                    </span>

                                    <strong>
                                        {{ $inactiveProducts }}
                                    </strong>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between">
                                    <span>
                                        <i class="bi bi-pause-circle text-warning"></i>
                                        Deactivated
                                    </span>

                                    <strong>
                                        {{ $deactivatedProducts }}
                                    </strong>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between">
                                    <span>
                                        <i class="bi bi-archive text-danger"></i>
                                        Archived
                                    </span>

                                    <strong>
                                        {{ $archivedProducts }}
                                    </strong>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Recent Events --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-dark text-white
                    d-flex justify-content-between align-items-center">

            <span>
                <i class="bi bi-clock-history"></i>
                Recent Events
            </span>

            <span class="badge bg-light text-dark">
                Latest 10
            </span>

        </div>

        <div class="card-body p-0">

            @if($recentEvents->count())

            <div class="table-responsive">

                <table class="table table-hover
                                  table-striped
                                  align-middle
                                  mb-0">

                    <thead class="table-light">

                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Event</th>
                            <th>Old Value</th>
                            <th>New Value</th>
                            <th>Date & Time</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach($recentEvents as $event)

                        @php
                        $badge = match($event->event) {
                        'activated' => 'success',
                        'deactivated' => 'warning',
                        'archived' => 'danger',
                        'statusChanged' => 'primary',
                        'priceChanged' => 'info',
                        default => 'secondary',
                        };
                        @endphp

                        <tr>

                            <td>
                                {{ $event->id }}
                            </td>

                            <td>
                                @if($event->product)
                                <strong>
                                    {{ $event->product->name }}
                                </strong>

                                <small class="text-muted d-block">
                                    ID: {{ $event->product->id }}
                                </small>
                                @else
                                <span class="text-muted">
                                    Product Deleted
                                </span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-{{ $badge }}">
                                    {{ $event->event }}
                                </span>
                            </td>

                            <td>
                                @if($event->old_value !== null)
                                {{ $event->old_value }}
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>
                                @if($event->new_value !== null)
                                {{ $event->new_value }}
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>
                                <span title="{{ $event->created_at }}">
                                    {{ $event->created_at?->format('d M Y, h:i A') }}
                                </span>
                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            @else

            <div class="text-center py-5">

                <i class="bi bi-inbox fs-1 text-muted"></i>

                <h5 class="mt-3">
                    No Events Found
                </h5>

                <p class="text-muted mb-0">
                    Product events will appear here when an event occurs.
                </p>

            </div>

            @endif

        </div>

    </div>

</div>

@endsection