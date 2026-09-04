<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductEventNotification extends Model
{
    protected $fillable = [
        'product_id',
        'event',
        'title',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Product relationship.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Event badge class.
     */
    public function getEventBadgeAttribute(): string
    {
        return match ($this->event) {
            'activated' => 'success',
            'deactivated' => 'warning',
            'archived' => 'danger',
            'statusChanged' => 'primary',
            'priceChanged' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Event icon.
     */
    public function getEventIconAttribute(): string
    {
        return match ($this->event) {
            'activated' => 'bi-check-circle-fill',
            'deactivated' => 'bi-pause-circle-fill',
            'archived' => 'bi-archive-fill',
            'statusChanged' => 'bi-arrow-repeat',
            'priceChanged' => 'bi-currency-rupee',
            default => 'bi-bell-fill',
        };
    }
}