<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'inventory_category_id',
        'quantity',
        'unit_price',
        'location',
        'minimum_stock',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'minimum_stock' => 'integer',
    ];
    
    /**
     * Get the category that owns the inventory item.
     */
    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id');
    }
    
    /**
     * Check if item is low on stock.
     */
    public function isLowStock()
    {
        return $this->quantity <= $this->minimum_stock;
    }
}
