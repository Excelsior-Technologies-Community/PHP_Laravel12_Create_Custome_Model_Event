@extends('products.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">


<div>
    <h4 class="mb-1">Products</h4>

    <small class="text-muted">
        Custom Model Events & Observer Management
    </small>
</div>

<div class="d-flex gap-2">

    <a
        href="{{ route('products.dashboard') }}"
        class="btn btn-info text-white"
    >
        📊 Dashboard
    </a>

    <a
        href="{{ route('products.create') }}"
        class="btn btn-primary"
    >
        + Add Product
    </a>

</div>

</div>

{{-- ============================================================
BULK ACTION FORM
============================================================ --}}

<form
    action="{{ route('products.bulk-action') }}"
    method="POST"
    id="bulkActionForm"
>
    @csrf


<div class="card shadow-sm mb-3">

    <div class="card-body">

        <div class="row align-items-center g-2">

            <div class="col-md-4">

                <div class="form-check">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="selectAll"
                    >

                    <label
                        class="form-check-label fw-semibold"
                        for="selectAll"
                    >
                        Select All Products
                    </label>

                </div>

            </div>

            <div class="col-md-5">

                <select
                    name="action"
                    id="bulkAction"
                    class="form-select"
                >

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

            <div class="col-md-3">

                <button
                    type="submit"
                    class="btn btn-dark w-100"
                    id="bulkSubmit"
                    disabled
                >
                    Apply to Selected
                </button>

            </div>

        </div>

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

{{-- ============================================================
PRODUCTS TABLE
IMPORTANT: The table is OUTSIDE the bulk form.
This prevents nested forms.
============================================================ --}}

<div class="table-responsive">


<table class="table table-bordered bg-white shadow-sm">

    <thead class="table-dark">

        <tr>

            <th style="width:50px">
                #
            </th>

            <th style="width:60px">
                Select
            </th>

            <th>
                Name
            </th>

            <th>
                Price (₹)
            </th>

            <th>
                Status
            </th>

            <th>
                Activated At
            </th>

            <th>
                Actions
            </th>

        </tr>

    </thead>

    <tbody>

        @forelse($products as $product)

        <tr>

            <td>
                {{ $product->id }}
            </td>

            {{-- Checkbox is NOT inside the bulk form anymore.
                 JavaScript will add selected IDs to the form
                 before submission. --}}
            <td class="text-center">

                <input
                    class="form-check-input product-checkbox"
                    type="checkbox"
                    value="{{ $product->id }}"
                >

            </td>

            <td>

                <strong>
                    {{ $product->name }}
                </strong>

            </td>

            <td>

                ₹{{ number_format($product->price) }}

            </td>

            <td>

                <span
                    class="badge bg-{{ $product->status_badge }}"
                >
                    {{ $product->status_label }}
                </span>

            </td>

            <td>

                {{ $product->activated_at ?? '—' }}

            </td>

            <td>

                <div class="d-flex gap-1 flex-wrap">

                    {{-- Edit --}}

                    <a
                        href="{{ route('products.edit', $product) }}"
                        class="btn btn-sm btn-warning"
                    >
                        Edit
                    </a>


                    {{-- Activate --}}

                    @if($product->status != 2)

                    <form
                        action="{{ route('products.activate', $product) }}"
                        method="POST"
                    >

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn btn-sm btn-success"
                        >
                            Activate
                        </button>

                    </form>

                    @endif


                    {{-- Deactivate --}}

                    @if($product->status == 2)

                    <form
                        action="{{ route('products.deactivate', $product) }}"
                        method="POST"
                    >

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn btn-sm btn-secondary"
                        >
                            Deactivate
                        </button>

                    </form>

                    @endif


                    {{-- Archive --}}

                    @if($product->status != 3)

                    <form
                        action="{{ route('products.archive', $product) }}"
                        method="POST"
                    >

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn btn-sm btn-danger"
                        >
                            Archive
                        </button>

                    </form>

                    @endif


                    {{-- Logs --}}

                    <a
                        href="{{ route('products.logs', $product) }}"
                        class="btn btn-sm btn-info text-white"
                    >
                        Logs
                    </a>


                    {{-- Delete --}}

                    <form
                        action="{{ route('products.destroy', $product) }}"
                        method="POST"
                        onsubmit="return confirm('Delete this product?')"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-sm btn-outline-danger"
                        >
                            Delete
                        </button>

                    </form>

                </div>

            </td>

        </tr>

        @empty

        <tr>

            <td
                colspan="7"
                class="text-center text-muted py-4"
            >
                No products found.
            </td>

        </tr>

        @endforelse

    </tbody>

</table>

</div>

@endsection

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const bulkForm =
        document.getElementById('bulkActionForm');

    const selectAll =
        document.getElementById('selectAll');

    const checkboxes =
        document.querySelectorAll('.product-checkbox');

    const selectedCount =
        document.getElementById('selectedCount');

    const bulkSubmit =
        document.getElementById('bulkSubmit');

    const bulkAction =
        document.getElementById('bulkAction');


    /*
    |--------------------------------------------------------------------------
    | Update Selection
    |--------------------------------------------------------------------------
    */

    function updateSelection() {

        const checked =
            document.querySelectorAll(
                '.product-checkbox:checked'
            );

        selectedCount.textContent = checked.length;

        bulkSubmit.disabled =
            checked.length === 0 ||
            bulkAction.value === '';

    }


    /*
    |--------------------------------------------------------------------------
    | Select All
    |--------------------------------------------------------------------------
    */

    selectAll.addEventListener('change', function () {

        checkboxes.forEach(function (checkbox) {

            checkbox.checked =
                selectAll.checked;

        });

        updateSelection();

    });


    /*
    |--------------------------------------------------------------------------
    | Individual Checkbox
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Bulk Action Dropdown
    |--------------------------------------------------------------------------
    */

    bulkAction.addEventListener('change', function () {

        updateSelection();

    });


    /*
    |--------------------------------------------------------------------------
    | Bulk Form Submit
    |--------------------------------------------------------------------------
    */

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


        const actionText =
            bulkAction.options[
                bulkAction.selectedIndex
            ].text;


        const confirmed = confirm(
            `${actionText} for ${checked.length} selected product(s)?`
        );


        if (!confirmed) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Remove previously generated hidden inputs
        |--------------------------------------------------------------------------
        */

        bulkForm
            .querySelectorAll(
                'input[name="product_ids[]"]'
            )
            .forEach(function (input) {

                input.remove();

            });


        /*
        |--------------------------------------------------------------------------
        | Add selected product IDs to bulk form
        |--------------------------------------------------------------------------
        */

        checked.forEach(function (checkbox) {

            const hiddenInput =
                document.createElement('input');

            hiddenInput.type = 'hidden';

            hiddenInput.name =
                'product_ids[]';

            hiddenInput.value =
                checkbox.value;

            bulkForm.appendChild(hiddenInput);

        });


        /*
        |--------------------------------------------------------------------------
        | Submit the actual bulk form
        |--------------------------------------------------------------------------
        */

        bulkForm.submit();

    });


    /*
    |--------------------------------------------------------------------------
    | Initial State
    |--------------------------------------------------------------------------
    */

    updateSelection();

});

</script>

@endpush
