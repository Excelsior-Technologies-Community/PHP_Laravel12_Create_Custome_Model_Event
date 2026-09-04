@extends('products.layout')

@section('content')

<div class="container py-4">

    <div class="row justify-content-center">

        <div class="col-md-7 col-lg-6">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-warning py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Product #{{ $product->id }}
                    </h5>
                </div>

                <div class="card-body p-4">

                    <form
                        action="{{ route('products.update', $product) }}"
                        method="POST"
                    >

                        @csrf
                        @method('PUT')

                        {{-- Product Name --}}
                        <div class="mb-3">

                            <label
                                for="name"
                                class="form-label fw-semibold"
                            >
                                Product Name
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $product->name) }}"
                                placeholder="e.g. Laptop"
                                required
                            >

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Price --}}
                        <div class="mb-4">

                            <label
                                for="price"
                                class="form-label fw-semibold"
                            >
                                Price (₹)
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    ₹
                                </span>

                                <input
                                    type="number"
                                    id="price"
                                    name="price"
                                    class="form-control @error('price') is-invalid @enderror"
                                    value="{{ old('price', $product->price) }}"
                                    min="0"
                                    step="1"
                                    required
                                >

                            </div>

                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Changing the price will fire the
                                <strong>priceChanged</strong> event and create
                                a notification.
                            </div>

                            @error('price')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Actions --}}
                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-warning"
                            >
                                <i class="bi bi-check-circle me-1"></i>
                                Update Product
                            </button>

                            <a
                                href="{{ route('products.index') }}"
                                class="btn btn-secondary"
                            >
                                <i class="bi bi-arrow-left me-1"></i>
                                Cancel
                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection