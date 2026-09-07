<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductEventNotification;
use App\Models\ProductStatusLog;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    /**
     * Status changed.
     */
    public function statusChanged(Product $product): void
    {
        $oldStatus = Product::statusName(
            $product->oldStatus
        );

        $newStatus = Product::statusName(
            $product->status
        );

        Log::info('Product status changed.', [
            'product_id' => $product->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);

        ProductStatusLog::create([
            'product_id' => $product->id,
            'event' => 'statusChanged',
            'old_value' => $oldStatus,
            'new_value' => $newStatus,
        ]);

        $this->createNotification(
            $product,
            'statusChanged',
            'Product Status Changed',
            "Product \"{$product->name}\" changed from {$oldStatus} to {$newStatus}."
        );
    }

    /**
     * Activated.
     */
    public function activated(Product $product): void
    {
        Log::info('Product activated.', [
            'product_id' => $product->id,
            'product_name' => $product->name,
        ]);

        $timestamps = $product->timestamps;

        $product->timestamps = false;

        $product->activated_at = now();

        $product->save();

        $product->timestamps = $timestamps;

        ProductStatusLog::create([
            'product_id' => $product->id,
            'event' => 'activated',
            'old_value' => Product::statusName(
                $product->oldStatus
            ),
            'new_value' => Product::statusName(
                $product->status
            ),
        ]);

        $this->createNotification(
            $product,
            'activated',
            'Product Activated',
            "Product \"{$product->name}\" is now active."
        );
    }

    /**
     * Deactivated.
     */
    public function deactivated(Product $product): void
    {
        Log::info('Product deactivated.', [
            'product_id' => $product->id,
            'product_name' => $product->name,
        ]);

        $timestamps = $product->timestamps;

        $product->timestamps = false;

        $product->deactivated_at = now();

        $product->save();

        $product->timestamps = $timestamps;

        ProductStatusLog::create([
            'product_id' => $product->id,
            'event' => 'deactivated',
            'old_value' => Product::statusName(
                $product->oldStatus
            ),
            'new_value' => Product::statusName(
                $product->status
            ),
        ]);

        $this->createNotification(
            $product,
            'deactivated',
            'Product Deactivated',
            "Product \"{$product->name}\" has been deactivated."
        );
    }

    /**
     * Archived.
     */
    public function archived(Product $product): void
    {
        Log::info('Product archived.', [
            'product_id' => $product->id,
            'product_name' => $product->name,
        ]);

        $timestamps = $product->timestamps;

        $product->timestamps = false;

        $product->archived_at = now();

        $product->save();

        $product->timestamps = $timestamps;

        ProductStatusLog::create([
            'product_id' => $product->id,
            'event' => 'archived',
            'old_value' => Product::statusName(
                $product->oldStatus
            ),
            'new_value' => Product::statusName(
                $product->status
            ),
        ]);

        $this->createNotification(
            $product,
            'archived',
            'Product Archived',
            "Product \"{$product->name}\" has been archived."
        );
    }

    /**
     * Price changed.
     */
    public function priceChanged(Product $product): void
    {
        Log::info('Product price changed.', [
            'product_id' => $product->id,
            'old_price' => $product->oldPrice,
            'new_price' => $product->price,
        ]);

        ProductStatusLog::create([
            'product_id' => $product->id,
            'event' => 'priceChanged',
            'old_value' => $product->oldPrice,
            'new_value' => $product->price,
        ]);

        $this->createNotification(
            $product,
            'priceChanged',
            'Product Price Changed',
            "Product \"{$product->name}\" price changed from ₹{$product->oldPrice} to ₹{$product->price}."
        );
    }

    /**
     * Create notification.
     */
    private function createNotification(
        Product $product,
        string $event,
        string $title,
        string $message
    ): void {
        ProductEventNotification::create([
            'product_id' => $product->id,
            'event' => $event,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }
}
