<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Test route to activate product and trigger event
     */
    public function index()
    {
        // Find product
        $product = Product::find(1);

        // Activate the product (fires custom event)
        $product->makeActive();

        // Dump and check
        dd($product);
    }
}
