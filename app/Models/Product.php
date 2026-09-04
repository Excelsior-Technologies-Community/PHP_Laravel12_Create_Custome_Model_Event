<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'status',
        'activated_at',
        'deactivated_at',
        'archived_at',
    ];

    /**
     * Custom observable model events.
     */
    protected $observables = [
        'activated',
        'deactivated',
        'archived',
        'priceChanged',
        'statusChanged',
    ];

    /**
     * Temporary properties used by observers.
     * These are not stored in the database.
     */
    public $oldPrice = null;
    public $oldStatus = null;

    /**
     * Product status constants.
     */
    public const STATUS_INACTIVE = 0;
    public const STATUS_DEACTIVATED = 1;
    public const STATUS_ACTIVE = 2;
    public const STATUS_ARCHIVED = 3;

    /**
     * Relationship with status logs.
     */
    public function statusLogs()
    {
        return $this->hasMany(ProductStatusLog::class);
    }

    /**
     * Product event notifications.
     */
    public function eventNotifications(): HasMany
    {
        return $this->hasMany(ProductEventNotification::class);
    }

    /**
     * Activate product.
     */
    public function makeActive()
    {
        $this->changeStatus(self::STATUS_ACTIVE, 'activated');
    }

    /**
     * Deactivate product.
     */
    public function makeDeactive()
    {
        $this->changeStatus(self::STATUS_DEACTIVATED, 'deactivated');
    }

    /**
     * Archive product.
     */
    public function makeArchived()
    {
        $this->changeStatus(self::STATUS_ARCHIVED, 'archived');
    }

    /**
     * Generic status changing method.
     *
     * This method fires:
     * 1. statusChanged
     * 2. Specific event such as activated/deactivated/archived
     */
    protected function changeStatus(int $newStatus, string $specificEvent): void
    {
        $this->oldStatus = $this->status;

        // Do nothing if status is already the requested status.
        if ((int) $this->status === $newStatus) {
            return;
        }

        $this->update([
            'status' => $newStatus,
        ]);

        /**
         * Generic custom event.
         */
        $this->fireModelEvent('statusChanged', false);

        /**
         * Existing specific custom event.
         */
        $this->fireModelEvent($specificEvent, false);
    }

    /**
     * Change product price.
     */
    public function changePrice(int $newPrice)
    {
        $this->oldPrice = $this->price;

        $this->update([
            'price' => $newPrice,
        ]);

        $this->fireModelEvent('priceChanged', false);
    }

    /**
     * Status label accessor.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_DEACTIVATED => 'Deactivated',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_ARCHIVED => 'Archived',
            default => 'Unknown',
        };
    }

    /**
     * Bootstrap badge color accessor.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ((int) $this->status) {
            self::STATUS_INACTIVE => 'secondary',
            self::STATUS_DEACTIVATED => 'warning',
            self::STATUS_ACTIVE => 'success',
            self::STATUS_ARCHIVED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Convert status number into readable text.
     */
    public static function statusName($status): string
    {
        return match ((int) $status) {
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_DEACTIVATED => 'Deactivated',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_ARCHIVED => 'Archived',
            default => 'Unknown',
        };
    }
}
