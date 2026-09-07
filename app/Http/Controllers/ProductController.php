<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /**
     * Product listing.
     *
     * Features:
     * - Search
     * - Status filter
     * - Sorting
     * - Pagination
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $sort = $request->get('sort', 'latest');
        $perPage = 5;

        $allowedSorts = [
            'latest',
            'oldest',
            'name_asc',
            'name_desc',
            'price_asc',
            'price_desc',
        ];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'latest';
        }

        $products = Product::query()

            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%");
                });
            })

            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('status', (int) $status);
            });

        switch ($sort) {
            case 'oldest':
                $products->oldest();
                break;

            case 'name_asc':
                $products->orderBy('name', 'asc');
                break;

            case 'name_desc':
                $products->orderBy('name', 'desc');
                break;

            case 'price_asc':
                $products->orderBy('price', 'asc');
                break;

            case 'price_desc':
                $products->orderBy('price', 'desc');
                break;

            default:
                $products->latest();
                break;
        }

        $products = $products
            ->paginate($perPage)
            ->withQueryString();

        return view('products.index', compact(
            'products',
            'search',
            'status',
            'sort'
        ));
    }

    /**
     * Create product page.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:1',
        ]);

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'status' => Product::STATUS_INACTIVE,
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show product.
     */
    public function show(Product $product)
    {
        return redirect()->route('products.index');
    }

    /**
     * Edit product.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update product.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:1',
        ]);

        if ((int) $product->price !== (int) $request->price) {
            $product->changePrice((int) $request->price);
        }

        $product->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Delete product.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted!');
    }

    /**
     * Activate product.
     */
    public function activate(Product $product)
    {
        $product->makeActive();

        return redirect()
            ->route('products.index')
            ->with('success', "Product #{$product->id} Activated!");
    }

    /**
     * Deactivate product.
     */
    public function deactivate(Product $product)
    {
        $product->makeDeactive();

        return redirect()
            ->route('products.index')
            ->with('success', "Product #{$product->id} Deactivated!");
    }

    /**
     * Archive product.
     */
    public function archive(Product $product)
    {
        $product->makeArchived();

        return redirect()
            ->route('products.index')
            ->with('success', "Product #{$product->id} Archived!");
    }

    /**
     * Product logs.
     *
     * Features:
     * - Search
     * - Event filter
     * - Date filter
     * - Pagination
     */
    public function logs(Request $request, Product $product)
    {
        $search = $request->get('search');
        $event = $request->get('event');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $logs = $product
            ->statusLogs()

            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('event', 'like', "%{$search}%")
                        ->orWhere('old_value', 'like', "%{$search}%")
                        ->orWhere('new_value', 'like', "%{$search}%");
                });
            })

            ->when($event, function ($query) use ($event) {
                $query->where('event', $event);
            })

            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })

            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })

            ->oldest()
            ->paginate(5)
            ->withQueryString();

        return view('products.logs', compact(
            'product',
            'logs',
            'search',
            'event',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Event Analytics Dashboard.
     *
     * Features:
     * - Date range filter
     * - Product statistics
     * - Event statistics
     * - Recent events
     */
  public function dashboard(Request $request)
{
    $dateFrom = $request->get('date_from');
    $dateTo = $request->get('date_to');

    /*
    |--------------------------------------------------------------------------
    | Product Counts
    |--------------------------------------------------------------------------
    */

    $productQuery = Product::query();

    $totalProducts = (clone $productQuery)->count();

    $activeProducts = (clone $productQuery)
        ->where('status', Product::STATUS_ACTIVE)
        ->count();

    $inactiveProducts = (clone $productQuery)
        ->where('status', Product::STATUS_INACTIVE)
        ->count();

    $deactivatedProducts = (clone $productQuery)
        ->where('status', Product::STATUS_DEACTIVATED)
        ->count();

    $archivedProducts = (clone $productQuery)
        ->where('status', Product::STATUS_ARCHIVED)
        ->count();


    /*
    |--------------------------------------------------------------------------
    | Event Query
    |--------------------------------------------------------------------------
    */

    $eventQuery = ProductStatusLog::query();

    if ($dateFrom) {
        $eventQuery->whereDate(
            'created_at',
            '>=',
            $dateFrom
        );
    }

    if ($dateTo) {
        $eventQuery->whereDate(
            'created_at',
            '<=',
            $dateTo
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Event Counts
    |--------------------------------------------------------------------------
    */

    $eventCounts = (clone $eventQuery)
        ->select(
            'event',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('event')
        ->orderByDesc('total')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | Total Events
    |--------------------------------------------------------------------------
    */

    $totalEvents = (clone $eventQuery)->count();


    /*
    |--------------------------------------------------------------------------
    | Recent Events
    |--------------------------------------------------------------------------
    */

    $recentEvents = (clone $eventQuery)
        ->with('product')
        ->latest()
        ->take(5)
        ->get();


    /*
    |--------------------------------------------------------------------------
    | Dashboard View
    |--------------------------------------------------------------------------
    */

    return view(
        'products.dashboard',
        compact(
            'totalProducts',
            'activeProducts',
            'inactiveProducts',
            'deactivatedProducts',
            'archivedProducts',
            'eventCounts',
            'totalEvents',
            'recentEvents',
            'dateFrom',
            'dateTo'
        )
    );
}

    /**
     * Bulk product actions.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
            'action' => 'required|in:activate,deactivate,archive',
        ]);

        $products = Product::whereIn(
            'id',
            $validated['product_ids']
        )->get();

        $processed = 0;

        DB::transaction(function () use (
            $products,
            $validated,
            &$processed
        ) {
            foreach ($products as $product) {
                switch ($validated['action']) {
                    case 'activate':
                        $product->makeActive();
                        break;

                    case 'deactivate':
                        $product->makeDeactive();
                        break;

                    case 'archive':
                        $product->makeArchived();
                        break;
                }

                $processed++;
            }
        });

        $actionLabel = match ($validated['action']) {
            'activate' => 'activated',
            'deactivate' => 'deactivated',
            'archive' => 'archived',
        };

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                "{$processed} product(s) {$actionLabel} successfully!"
            );
    }

    /**
     * Export products to CSV.
     *
     * Respects:
     * - Search
     * - Status filter
     * - Sorting
     */
    public function export(Request $request): StreamedResponse
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $sort = $request->get('sort', 'oldest');

        $query = Product::query()

            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%");
                });
            })

            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('status', (int) $status);
            });

        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;

            case 'name_asc':
                $query->orderBy('name');
                break;

            case 'name_desc':
                $query->orderByDesc('name');
                break;

            case 'price_asc':
                $query->orderBy('price');
                break;

            case 'price_desc':
                $query->orderByDesc('price');
                break;

            default:
                $query->latest();
                break;
        }

        $products = $query->get();

        $filename = 'products-' . now()->format('Y-m-d-H-i-s') . '.csv';

        return response()->streamDownload(function () use ($products) {

            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Name',
                'Price',
                'Status',
                'Activated At',
                'Deactivated At',
                'Archived At',
                'Created At',
            ]);

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->id,
                    $product->name,
                    $product->price,
                    $product->status_label,
                    $product->activated_at,
                    $product->deactivated_at,
                    $product->archived_at,
                    $product->created_at,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
