<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductStatusLog;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    public function activated(Product $product)
    {
        Log::info("✅ Product ACTIVATED — ID: {$product->id}");
        $product->timestamps = false;
        $product->activated_at = now();
        $product->save();
        $product->timestamps = true;

        ProductStatusLog::create([
            'product_id' => $product->id,
            'event'      => 'activated',
            'old_value'  => 'inactive',
            'new_value'  => 'active',
        ]);
    }

    public function deactivated(Product $product)
    {
        Log::info("⛔ Product DEACTIVATED — ID: {$product->id}");
        $product->timestamps = false;
        $product->deactivated_at = now();
        $product->save();
        $product->timestamps = true;

        ProductStatusLog::create([
            'product_id' => $product->id,
            'event'      => 'deactivated',
            'old_value'  => 'active',
            'new_value'  => 'deactivated',
        ]);
    }

    public function archived(Product $product)
    {
        Log::info("📦 Product ARCHIVED — ID: {$product->id}");
        $product->timestamps = false;
        $product->archived_at = now();
        $product->save();
        $product->timestamps = true;

        ProductStatusLog::create([
            'product_id' => $product->id,
            'event'      => 'archived',
            'old_value'  => 'active',
            'new_value'  => 'archived',
        ]);
    }

    public function priceChanged(Product $product)
    {
        $oldPrice = $product->oldPrice ?? 'N/A';
        Log::info("💰 Product PRICE CHANGED — ID: {$product->id} | Old: {$oldPrice} → New: {$product->price}");

        ProductStatusLog::create([
            'product_id' => $product->id,
            'event'      => 'priceChanged',
            'old_value'  => $oldPrice,
            'new_value'  => $product->price,
        ]);
    }
}
