@extends('products.layout')

@section('content')
<div class="card shadow-sm" style="max-width:500px">
    <div class="card-header bg-primary text-white">Add New Product</div>
    <div class="card-body">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="e.g. Laptop">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Price (₹)</label>
                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                       value="{{ old('price') }}" placeholder="e.g. 50000">
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
