<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'unit_type',
        'current_quantity',
        'minimum_quantity',
    ];

    protected $casts = [
        'current_quantity' => 'decimal:2',
        'minimum_quantity' => 'decimal:2',
    ];

    protected $appends = ['stock_status'];

    // Relationship
    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    // Search scope (Requirement 5)
    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            return $query->where('name', 'like', "%{$search}%");
        }
        return $query;
    }

    // Low stock scope
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_quantity', '<=', 'minimum_quantity')
                    ->whereNotNull('minimum_quantity');
    }

    // Stock status accessor
    public function getStockStatusAttribute(): string
    {
        if ($this->current_quantity <= 0) {
            return 'out_of_stock';
        }
        if ($this->minimum_quantity && $this->current_quantity <= $this->minimum_quantity) {
            return 'low_stock';
        }
        return 'in_stock';
    }
}