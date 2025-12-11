<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Fields that can be mass-assigned
    protected $fillable = [
        'name', 'price', 'status', 'activated_at'
    ];

    // Register a custom observable event named "activated"
    protected $observables = ['activated'];

    /**
     * Custom method to activate a product.
     * When called, it updates status & then fires custom event.
     */
    public function makeActive()
    { 
        // Set product status to active
        $this->update(['status' => 2]);

        // Fire the custom event
        $this->fireModelEvent('activated', false);
    }
}
