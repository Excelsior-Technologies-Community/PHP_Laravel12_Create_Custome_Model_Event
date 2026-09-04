<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStatusLog extends Model
{
    protected $fillable = [
        'product_id',
        'event',
        'old_value',
        'new_value',
    ];

    /**
     * Product relationship.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}