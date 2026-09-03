<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|integer|min:1',
        ]);

        Product::create([
            'name'   => $request->name,
            'price'  => $request->price,
            'status' => 0,
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function show(Product $product)
    {
        return redirect()->route('products.index');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|integer|min:1',
        ]);

        // Fire priceChanged event if price is different
        if ($product->price != $request->price) {
            $product->changePrice((int) $request->price);
        }

        $product->update(['name' => $request->name]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted!');
    }

    // Custom Event Actions
    public function activate(Product $product)
    {
        $product->makeActive();
        return redirect()->route('products.index')->with('success', "Product #{$product->id} Activated!");
    }

    public function deactivate(Product $product)
    {
        $product->makeDeactive();
        return redirect()->route('products.index')->with('success', "Product #{$product->id} Deactivated!");
    }

    public function archive(Product $product)
    {
        $product->makeArchived();
        return redirect()->route('products.index')->with('success', "Product #{$product->id} Archived!");
    }

    public function logs(Product $product)
    {
        $logs = $product->statusLogs()->latest()->get();
        return view('products.logs', compact('product', 'logs'));
    }
}
