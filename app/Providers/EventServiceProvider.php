<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Models\Product;
use App\Observers\ProductObserver;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        //
    ];

    /**
     * Register Observers
     */
    public function boot()
    {
        // Attach the observer to the product model
        Product::observe(ProductObserver::class);
    }
}
