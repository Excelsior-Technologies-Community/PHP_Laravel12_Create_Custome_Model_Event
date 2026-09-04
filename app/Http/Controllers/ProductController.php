<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Product listing.
     */
    public function index()
    {
        $products = Product::latest()->get();

        return view('products.index', compact('products'));
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
            'name'  => 'required|string|max:255',
            'price' => 'required|integer|min:1',
        ]);

        Product::create([
            'name'   => $request->name,
            'price'  => $request->price,
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
            'name'  => 'required|string|max:255',
            'price' => 'required|integer|min:1',
        ]);

        /**
         * Fire priceChanged event if price changed.
         */
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
     */
    public function logs(Product $product)
    {
        $logs = $product
            ->statusLogs()
            ->latest()
            ->get();

        return view('products.logs', compact('product', 'logs'));
    }

    /**
     * Event Analytics Dashboard.
     */
    public function dashboard()
    {
        $totalProducts = Product::count();

        $activeCount = Product::where('status', Product::STATUS_ACTIVE)->count();

        $inactiveCount = Product::where('status', Product::STATUS_INACTIVE)->count();

        $deactivatedCount = Product::where(
            'status',
            Product::STATUS_DEACTIVATED
        )->count();

        $archivedCount = Product::where(
            'status',
            Product::STATUS_ARCHIVED
        )->count();

        $eventCounts = ProductStatusLog::select(
                'event',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('event')
            ->orderByDesc('total')
            ->get();

        $totalEvents = ProductStatusLog::count();

        $recentEvents = ProductStatusLog::with('product')
            ->latest()
            ->take(10)
            ->get();

        return view('products.dashboard', compact(
            'totalProducts',
            'activeCount',
            'inactiveCount',
            'deactivatedCount',
            'archivedCount',
            'eventCounts',
            'totalEvents',
            'recentEvents'
        ));
    }

    /**
     * Bulk product actions.
     *
     * Each product calls its own model method,
     * therefore custom model events and observers
     * are fired normally.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'product_ids'   => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
            'action'        => 'required|in:activate,deactivate,archive',
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
            'activate'   => 'activated',
            'deactivate' => 'deactivated',
            'archive'    => 'archived',
        };

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                "{$processed} product(s) {$actionLabel} successfully!"
            );
    }
}