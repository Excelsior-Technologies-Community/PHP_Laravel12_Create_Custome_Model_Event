@extends('products.layout')

@section('content')

<div class="container-fluid py-4">

```
{{-- Header --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="mb-1">Products</h3>
        <small class="text-muted">
            Custom Model Events & Observer Management
        </small>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('products.dashboard') }}"
           class="btn btn-info text-white">
            📊 Dashboard
        </a>

        <a href="{{ route('products.export', request()->query()) }}"
           class="btn btn-success">
            📥 Export CSV
        </a>

        <a href="{{ route('products.create') }}"
           class="btn btn-primary">
            + Add Product
        </a>
    </div>
</div>


{{-- Search / Filter / Sort --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">

        <form method="GET"
              action="{{ route('products.index') }}"
              class="row g-3">

            {{-- Search --}}
            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    Search
                </label>

                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       class="form-control"
                       placeholder="Search name, ID or price...">
            </div>


            {{-- Status --}}
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    Status
                </label>

                <select name="status"
                        class="form-select">

                    <option value="">
                        All Status
                    </option>

                    <option value="0"
                        {{ $status == '0' ? 'selected' : '' }}>
                        Inactive
                    </option>

                    <option value="1"
                        {{ $status == '1' ? 'selected' : '' }}>
                        Deactivated
                    </option>

                    <option value="2"
                        {{ $status == '2' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="3"
                        {{ $status == '3' ? 'selected' : '' }}>
                        Archived
                    </option>

                </select>
            </div>


            {{-- Sort --}}
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    Sort
                </label>

                <select name="sort"
                        class="form-select">

                    <option value="latest"
                        {{ $sort == 'latest' ? 'selected' : '' }}>
                        Latest
                    </option>

                    <option value="oldest"
                        {{ $sort == 'oldest' ? 'selected' : '' }}>
                        Oldest
                    </option>

                    <option value="name_asc"
                        {{ $sort == 'name_asc' ? 'selected' : '' }}>
                        Name A-Z
                    </option>

                    <option value="name_desc"
                        {{ $sort == 'name_desc' ? 'selected' : '' }}>
                        Name Z-A
                    </option>

                    <option value="price_asc"
                        {{ $sort == 'price_asc' ? 'selected' : '' }}>
                        Price Low-High
                    </option>

                    <option value="price_desc"
                        {{ $sort == 'price_desc' ? 'selected' : '' }}>
                        Price High-Low
                    </option>

                </select>
            </div>


            {{-- Filter Button --}}
            <div class="col-md-2 d-flex align-items-end gap-2">

                <button class="btn btn-primary w-100"
                        type="submit">
                    🔎 Filter
                </button>

                <a href="{{ route('products.index') }}"
                   class="btn btn-outline-secondary">
                    ✕
                </a>

            </div>

        </form>

    </div>
</div>


{{-- Bulk Action --}}
<form action="{{ route('products.bulk-action') }}"
      method="POST"
      id="bulkActionForm">

    @csrf

    <div class="card shadow-sm mb-3">
        <div class="card-body">

            <div class="row align-items-center g-2">

                {{-- Select All --}}
                <div class="col-md-4">

                    <div class="form-check">

                        <input class="form-check-input"
                               type="checkbox"
                               id="selectAll">

                        <label class="form-check-label fw-semibold"
                               for="selectAll">
                            Select All Products
                        </label>

                    </div>

                </div>


                {{-- Bulk Action --}}
                <div class="col-md-5">

                    <select name="action"
                            id="bulkAction"
                            class="form-select">

                        <option value="">
                            Select Bulk Action
                        </option>

                        <option value="activate">
                            ✅ Activate Selected
                        </option>

                        <option value="deactivate">
                            ⛔ Deactivate Selected
                        </option>

                        <option value="archive">
                            📦 Archive Selected
                        </option>

                    </select>

                </div>


                {{-- Apply --}}
                <div class="col-md-3">

                    <button type="submit"
                            class="btn btn-dark w-100"
                            id="bulkSubmit"
                            disabled>
                        Apply to Selected
                    </button>

                </div>

            </div>


            {{-- Selected Count --}}
            <div class="mt-2">

                <small class="text-muted">
                    Selected:
                    <strong id="selectedCount">0</strong>
                    product(s)
                </small>

            </div>

        </div>
    </div>

</form>


{{-- Products Table --}}
<div class="table-responsive">

    <table class="table table-bordered bg-white shadow-sm">

        <thead class="table-dark">

            <tr>
                <th>#</th>
                <th>Select</th>
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

                    {{-- Sequential Number --}}
                    <td>
                        {{ $products->firstItem() + $loop->index }}
                    </td>


                    {{-- Checkbox --}}
                    <td class="text-center">

                        <input class="form-check-input product-checkbox"
                               type="checkbox"
                               value="{{ $product->id }}">

                    </td>


                    {{-- Name --}}
                    <td>
                        <strong>
                            {{ $product->name }}
                        </strong>
                    </td>


                    {{-- Price --}}
                    <td>
                        ₹{{ number_format($product->price) }}
                    </td>


                    {{-- Status --}}
                    <td>

                        <span class="badge bg-{{ $product->status_badge }}">
                            {{ $product->status_label }}
                        </span>

                    </td>


                    {{-- Activated At --}}
                    <td>
                        {{ $product->activated_at ?? '—' }}
                    </td>


                    {{-- Actions --}}
                    <td>

                        <div class="d-flex gap-1 flex-wrap">

                            {{-- Edit --}}
                            <a href="{{ route('products.edit', $product) }}"
                               class="btn btn-sm btn-warning">
                                Edit
                            </a>


                            {{-- Activate --}}
                            @if($product->status != 2)

                                <form action="{{ route('products.activate', $product) }}"
                                      method="POST">

                                    @csrf

                                    <button type="submit"
                                            class="btn btn-sm btn-success">
                                        Activate
                                    </button>

                                </form>

                            @endif


                            {{-- Deactivate --}}
                            @if($product->status == 2)

                                <form action="{{ route('products.deactivate', $product) }}"
                                      method="POST">

                                    @csrf

                                    <button type="submit"
                                            class="btn btn-sm btn-secondary">
                                        Deactivate
                                    </button>

                                </form>

                            @endif


                            {{-- Archive --}}
                            @if($product->status != 3)

                                <form action="{{ route('products.archive', $product) }}"
                                      method="POST">

                                    @csrf

                                    <button type="submit"
                                            class="btn btn-sm btn-danger">
                                        Archive
                                    </button>

                                </form>

                            @endif


                            {{-- Logs --}}
                            <a href="{{ route('products.logs', $product) }}"
                               class="btn btn-sm btn-info text-white">
                                Logs
                            </a>


                            {{-- Delete --}}
                            <form action="{{ route('products.destroy', $product) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this product?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger">
                                    Delete
                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7"
                        class="text-center text-muted py-5">

                        No products found.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>


{{-- Numeric Only Pagination --}}
@if($products->hasPages())

    <div class="d-flex justify-content-center mt-4">

        <nav aria-label="Product pagination">

            <ul class="pagination">

                @for($page = 1; $page <= $products->lastPage(); $page++)

                    <li class="page-item
                        {{ $products->currentPage() == $page ? 'active' : '' }}">

                        <a class="page-link"
                           href="{{ request()->fullUrlWithQuery(['page' => $page]) }}">

                            {{ $page }}

                        </a>

                    </li>

                @endfor

            </ul>

        </nav>

    </div>

@endif
```

</div>

@endsection

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const bulkForm = document.getElementById('bulkActionForm');

    const selectAll = document.getElementById('selectAll');

    const checkboxes =
        document.querySelectorAll('.product-checkbox');

    const selectedCount =
        document.getElementById('selectedCount');

    const bulkSubmit =
        document.getElementById('bulkSubmit');

    const bulkAction =
        document.getElementById('bulkAction');


    function updateSelection() {

        const checked =
            document.querySelectorAll(
                '.product-checkbox:checked'
            );

        selectedCount.textContent =
            checked.length;

        bulkSubmit.disabled =
            checked.length === 0 ||
            bulkAction.value === '';

    }


    // Select All
    selectAll.addEventListener('change', function () {

        checkboxes.forEach(function (checkbox) {

            checkbox.checked =
                selectAll.checked;

        });

        updateSelection();

    });


    // Individual Checkbox
    checkboxes.forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {

            const checkedCount =
                document.querySelectorAll(
                    '.product-checkbox:checked'
                ).length;

            selectAll.checked =
                checkedCount === checkboxes.length &&
                checkboxes.length > 0;

            selectAll.indeterminate =
                checkedCount > 0 &&
                checkedCount < checkboxes.length;

            updateSelection();

        });

    });


    // Bulk Action Change
    bulkAction.addEventListener(
        'change',
        updateSelection
    );


    // Bulk Submit
    bulkForm.addEventListener('submit', function (event) {

        event.preventDefault();

        const checked =
            document.querySelectorAll(
                '.product-checkbox:checked'
            );


        if (checked.length === 0) {

            alert(
                'Please select at least one product.'
            );

            return;
        }


        if (!bulkAction.value) {

            alert(
                'Please select a bulk action.'
            );

            return;
        }


        if (!confirm(
            'Apply "' +
            bulkAction.options[
                bulkAction.selectedIndex
            ].text +
            '" to ' +
            checked.length +
            ' product(s)?'
        )) {

            return;

        }


        // Remove old hidden inputs
        bulkForm
            .querySelectorAll(
                'input[name="product_ids[]"]'
            )
            .forEach(function (input) {

                input.remove();

            });


        // Add selected IDs
        checked.forEach(function (checkbox) {

            const input =
                document.createElement('input');

            input.type = 'hidden';

            input.name = 'product_ids[]';

            input.value = checkbox.value;

            bulkForm.appendChild(input);

        });


        bulkForm.submit();

    });


    updateSelection();

});

</script>

@endpush
