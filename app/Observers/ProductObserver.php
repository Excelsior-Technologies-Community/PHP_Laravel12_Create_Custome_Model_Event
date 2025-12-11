<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Custom event handler for "activated".
     *
     * @param Product $product
     * @return void
     */
    public function activated(Product $product)
{
    \Log::info("🔥 OBSERVER FIRED for product ID: {$product->id}");

    $product->activated_at = now();
    $product->save();
}

}
