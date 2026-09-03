@extends('products.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Products List</h4>
    <a href="{{ route('products.create') }}" class="btn btn-primary">+ Add Product</a>
</div>

<table class="table table-bordered bg-white shadow-sm">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Price (₹)</th>
            <th>Status</th>
            <th>Activated At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ number_format($product->price) }}</td>
            <td>
                <span class="badge bg-{{ $product->status_badge }}">
                    {{ $product->status_label }}
                </span>
            </td>
            <td>{{ $product->activated_at ?? '—' }}</td>
            <td>
                <div class="d-flex gap-1 flex-wrap">
                    {{-- Edit --}}
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>

                    {{-- Activate --}}
                    @if($product->status != 2)
                    <form action="{{ route('products.activate', $product) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm btn-success">Activate</button>
                    </form>
                    @endif

                    {{-- Deactivate --}}
                    @if($product->status == 2)
                    <form action="{{ route('products.deactivate', $product) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm btn-secondary">Deactivate</button>
                    </form>
                    @endif

                    {{-- Archive --}}
                    @if($product->status != 3)
                    <form action="{{ route('products.archive', $product) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm btn-danger">Archive</button>
                    </form>
                    @endif

                    {{-- Logs --}}
                    <a href="{{ route('products.logs', $product) }}" class="btn btn-sm btn-info text-white">Logs</a>

                    {{-- Delete --}}
                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                          onsubmit="return confirm('Delete this product?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted">No products found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
