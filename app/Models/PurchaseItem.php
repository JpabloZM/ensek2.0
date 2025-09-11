<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_id',
        'inventory_item_id',
        'quantity',
        'unit_price',
        'total_price',
        'received_quantity',
        'status', // 'pending', 'partial', 'complete'
        'notes',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'received_quantity' => 'integer',
        'unit_price' => 'decimal:0',
        'total_price' => 'decimal:0',
    ];
    
    /**
     * Get the purchase that owns the item.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    
    /**
     * Get the inventory item that was purchased.
     */
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
    
    /**
     * Get the remaining quantity to be received.
     */
    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->received_quantity;
    }
    
    /**
     * Get the formatted unit price.
     */
    public function getFormattedUnitPriceAttribute()
    {
        return '$ ' . number_format($this->unit_price, 0, ',', '.');
    }
    
    /**
     * Get the formatted total price.
     */
    public function getFormattedTotalPriceAttribute()
    {
        return '$ ' . number_format($this->total_price, 0, ',', '.');
    }
    
    /**
     * Get the formatted status.
     */
    public function getFormattedStatusAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'Pendiente';
            case 'partial':
                return 'Recibido parcialmente';
            case 'complete':
                return 'Recibido completamente';
            default:
                return $this->status;
        }
    }
}
