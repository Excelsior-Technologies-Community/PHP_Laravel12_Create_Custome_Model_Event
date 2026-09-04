@extends('products.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>

        <h5 class="mb-1">
            📋 Event History
        </h5>

        <small class="text-muted">
            Product: {{ $product->name }} (#{{ $product->id }})
        </small>

    </div>

    <a
        href="{{ route('products.index') }}"
        class="btn btn-sm btn-secondary"
    >
        ← Back
    </a>

</div>


<div class="card shadow-sm">

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered mb-0">

                <thead class="table-dark">

                    <tr>

                        <th>#</th>

                        <th>Event</th>

                        <th>Old Value</th>

                        <th>New Value</th>

                        <th>Time</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($logs as $log)

                    <tr>

                        <td>
                            {{ $log->id }}
                        </td>

                        <td>

                            @php

                                $badges = [
                                    'activated'    => 'success',
                                    'deactivated'  => 'warning',
                                    'archived'     => 'danger',
                                    'priceChanged' => 'info',
                                    'statusChanged'=> 'primary',
                                ];

                                $badge =
                                    $badges[$log->event]
                                    ?? 'secondary';

                            @endphp

                            <span
                                class="badge bg-{{ $badge }} event-badge"
                            >
                                {{ $log->event }}
                            </span>

                        </td>

                        <td>
                            {{ $log->old_value ?? '—' }}
                        </td>

                        <td>
                            {{ $log->new_value ?? '—' }}
                        </td>

                        <td>
                            {{ $log->created_at->format('d M Y, h:i A') }}
                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center text-muted py-4"
                        >
                            No event logs found.
                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection