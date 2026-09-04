@extends('products.layout')

@section('content')

<div class="container py-4">

    <div class="row justify-content-center">

        <div class="col-md-7 col-lg-6">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add New Product
                    </h5>
                </div>

                <div class="card-body p-4">

                    <form
                        action="{{ route('products.store') }}"
                        method="POST"
                    >

                        @csrf

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
                                value="{{ old('name') }}"
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

                            <input
                                type="number"
                                id="price"
                                name="price"
                                class="form-control @error('price') is-invalid @enderror"
                                value="{{ old('price') }}"
                                placeholder="e.g. 50000"
                                min="0"
                                step="1"
                                required
                            >

                            @error('price')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Actions --}}
                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-check-circle me-1"></i>
                                Save Product
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