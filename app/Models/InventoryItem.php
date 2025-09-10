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
        'unit_of_measure',
        'barcode',
        'sku',
        'reorder_point', // Punto de reorden
        'is_active',
        'last_purchase_date',
        'last_purchase_price',
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
        'reorder_point' => 'integer',
        'is_active' => 'boolean',
        'last_purchase_date' => 'date',
        'last_purchase_price' => 'decimal:2',
    ];
    
    /**
     * Get the category that owns the inventory item.
     */
    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id');
    }
    
    /**
     * Get all movements for this inventory item.
     */
    public function movements()
    {
        return $this->hasMany(InventoryMovement::class, 'inventory_item_id');
    }
    
    /**
     * Get the providers of this item.
     */
    public function providers()
    {
        return $this->belongsToMany(Provider::class, 'provider_inventory_item')
            ->withPivot(['provider_code', 'provider_price', 'lead_time'])
            ->withTimestamps();
    }
    
    /**
     * Get all purchase items for this inventory item.
     */
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
    
    /**
     * Check if item is low on stock.
     */
    public function isLowStock()
    {
        return $this->quantity <= $this->minimum_stock;
    }
    
    /**
     * Get total value of inventory (quantity * unit_price)
     */
    public function getTotalValueAttribute()
    {
        return $this->quantity * $this->unit_price;
    }
    
    /**
     * Get the formatted unit price.
     */
    public function getFormattedUnitPriceAttribute()
    {
        return '$' . number_format($this->unit_price, 2);
    }
    
    /**
     * Get the formatted total value.
     */
    public function getFormattedTotalValueAttribute()
    {
        return '$' . number_format($this->total_value, 2);
    }
}
