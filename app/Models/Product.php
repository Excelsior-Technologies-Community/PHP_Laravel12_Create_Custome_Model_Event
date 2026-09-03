<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'status', 'activated_at', 'deactivated_at', 'archived_at'];

    // Register all custom observable events
    protected $observables = ['activated', 'deactivated', 'archived', 'priceChanged'];

    // Relationship to status logs
    public function statusLogs()
    {
        return $this->hasMany(ProductStatusLog::class);
    }

    // Temporary property to pass old price to observer (not saved to DB)
    public $oldPrice = null;

    // Status: 0=inactive, 1=deactivated, 2=active, 3=archived
    public function makeActive()
    {
        $this->update(['status' => 2]);
        $this->fireModelEvent('activated', false);
    }

    public function makeDeactive()
    {
        $this->update(['status' => 1]);
        $this->fireModelEvent('deactivated', false);
    }

    public function makeArchived()
    {
        $this->update(['status' => 3]);
        $this->fireModelEvent('archived', false);
    }

    public function changePrice(int $newPrice)
    {
        $this->oldPrice = $this->price;
        $this->update(['price' => $newPrice]);
        $this->fireModelEvent('priceChanged', false);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            0 => 'Inactive',
            1 => 'Deactivated',
            2 => 'Active',
            3 => 'Archived',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            0 => 'secondary',
            1 => 'warning',
            2 => 'success',
            3 => 'danger',
            default => 'secondary',
        };
    }
}
