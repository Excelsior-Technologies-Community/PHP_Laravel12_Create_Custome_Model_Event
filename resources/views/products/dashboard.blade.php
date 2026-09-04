@extends('products.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="mb-1">
            📊 Event Analytics Dashboard
        </h3>

        <p class="text-muted mb-0">
            Monitor Product Model Events and Observer activity.
        </p>

    </div>

    <a
        href="{{ route('products.index') }}"
        class="btn btn-secondary"
    >
        ← Products
    </a>

</div>


{{-- Product Statistics --}}

<div class="row g-3 mb-4">

    <div class="col-md-4">

        <div class="card shadow-sm stat-card">

            <div class="card-body">

                <small class="text-muted">
                    Total Products
                </small>

                <h2 class="mb-0">
                    {{ $totalProducts }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card shadow-sm stat-card">

            <div class="card-body">

                <small class="text-muted">
                    Active Products
                </small>

                <h2 class="text-success mb-0">
                    {{ $activeCount }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card shadow-sm stat-card">

            <div class="card-body">

                <small class="text-muted">
                    Inactive Products
                </small>

                <h2 class="text-secondary mb-0">
                    {{ $inactiveCount }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card shadow-sm stat-card">

            <div class="card-body">

                <small class="text-muted">
                    Deactivated Products
                </small>

                <h2 class="text-warning mb-0">
                    {{ $deactivatedCount }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card shadow-sm stat-card">

            <div class="card-body">

                <small class="text-muted">
                    Archived Products
                </small>

                <h2 class="text-danger mb-0">
                    {{ $archivedCount }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card shadow-sm stat-card">

            <div class="card-body">

                <small class="text-muted">
                    Total Events
                </small>

                <h2 class="text-primary mb-0">
                    {{ $totalEvents }}
                </h2>

            </div>

        </div>

    </div>

</div>


<div class="row g-4">


    {{-- Event Counts --}}

    <div class="col-lg-5">

        <div class="card shadow-sm h-100">

            <div class="card-header bg-dark text-white">

                <strong>
                    📈 Event Statistics
                </strong>

            </div>

            <div class="card-body">

                @forelse($eventCounts as $event)

                    @php

                        $badge = match ($event->event) {
                            'activated'     => 'success',
                            'deactivated'   => 'warning',
                            'archived'      => 'danger',
                            'priceChanged'  => 'info',
                            'statusChanged' => 'primary',
                            default         => 'secondary',
                        };

                    @endphp

                    <div
                        class="d-flex justify-content-between align-items-center border-bottom py-3"
                    >

                        <div>

                            <span
                                class="badge bg-{{ $badge }}"
                            >
                                {{ $event->event }}
                            </span>

                        </div>

                        <strong>
                            {{ $event->total }}
                        </strong>

                    </div>

                @empty

                    <div class="text-center text-muted py-4">
                        No events recorded yet.
                    </div>

                @endforelse

            </div>

        </div>

    </div>


    {{-- Recent Events --}}

    <div class="col-lg-7">

        <div class="card shadow-sm">

            <div class="card-header bg-dark text-white">

                <strong>
                    🕒 Recent Events
                </strong>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead>

                            <tr>

                                <th>
                                    Product
                                </th>

                                <th>
                                    Event
                                </th>

                                <th>
                                    Change
                                </th>

                                <th>
                                    Time
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($recentEvents as $log)

                            <tr>

                                <td>

                                    @if($log->product)

                                        <strong>
                                            {{ $log->product->name }}
                                        </strong>

                                        <br>

                                        <small class="text-muted">
                                            #{{ $log->product->id }}
                                        </small>

                                    @else

                                        <span class="text-muted">
                                            Deleted Product
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @php

                                        $badge = match ($log->event) {
                                            'activated'     => 'success',
                                            'deactivated'   => 'warning',
                                            'archived'      => 'danger',
                                            'priceChanged'  => 'info',
                                            'statusChanged' => 'primary',
                                            default         => 'secondary',
                                        };

                                    @endphp

                                    <span
                                        class="badge bg-{{ $badge }}"
                                    >
                                        {{ $log->event }}
                                    </span>

                                </td>

                                <td>

                                    <span class="text-muted">
                                        {{ $log->old_value ?? '—' }}
                                    </span>

                                    <strong>
                                        →
                                    </strong>

                                    <span>
                                        {{ $log->new_value ?? '—' }}
                                    </span>

                                </td>

                                <td>

                                    <small>
                                        {{ $log->created_at->format('d M Y, h:i A') }}
                                    </small>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="text-center text-muted py-4"
                                >
                                    No recent events.
                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection