@extends('products.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5>📋 Status Logs — {{ $product->name }}</h5>
    <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary">← Back</a>
</div>

<table class="table table-bordered bg-white shadow-sm">
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
            <td>{{ $log->id }}</td>
            <td>
                @php
                    $badges = ['activated'=>'success','deactivated'=>'warning','archived'=>'danger','priceChanged'=>'info'];
                    $badge = $badges[$log->event] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $badge }}">{{ $log->event }}</span>
            </td>
            <td>{{ $log->old_value ?? '—' }}</td>
            <td>{{ $log->new_value ?? '—' }}</td>
            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted">No logs found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
