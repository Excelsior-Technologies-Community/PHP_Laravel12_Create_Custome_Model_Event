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

    protected $observables = [
        'activated',
        'deactivated',
        'archived',
        'priceChanged',
        'statusChanged',
    ];

    public $oldPrice = null;

    public $oldStatus = null;

    public const STATUS_INACTIVE = 0;

    public const STATUS_DEACTIVATED = 1;

    public const STATUS_ACTIVE = 2;

    public const STATUS_ARCHIVED = 3;

    /**
     * Status logs relationship.
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(ProductStatusLog::class);
    }

    /**
     * Notifications relationship.
     */
    public function eventNotifications(): HasMany
    {
        return $this->hasMany(ProductEventNotification::class);
    }

    /**
     * Activate product.
     */
    public function makeActive(): void
    {
        $this->changeStatus(
            self::STATUS_ACTIVE,
            'activated'
        );
    }

    /**
     * Deactivate product.
     */
    public function makeDeactive(): void
    {
        $this->changeStatus(
            self::STATUS_DEACTIVATED,
            'deactivated'
        );
    }

    /**
     * Archive product.
     */
    public function makeArchived(): void
    {
        $this->changeStatus(
            self::STATUS_ARCHIVED,
            'archived'
        );
    }

    /**
     * Change status.
     */
    protected function changeStatus(
        int $newStatus,
        string $specificEvent
    ): void {
        $this->oldStatus = $this->status;

        if ((int) $this->status === $newStatus) {
            return;
        }

        $this->update([
            'status' => $newStatus,
        ]);

        $this->fireModelEvent(
            'statusChanged',
            false
        );

        $this->fireModelEvent(
            $specificEvent,
            false
        );
    }

    /**
     * Change price.
     */
    public function changePrice(int $newPrice): void
    {
        $this->oldPrice = $this->price;

        $this->update([
            'price' => $newPrice,
        ]);

        $this->fireModelEvent(
            'priceChanged',
            false
        );
    }

    /**
     * Status label.
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
     * Status badge.
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
     * Status name.
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