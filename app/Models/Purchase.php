<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider_id',
        'reference_number',
        'purchase_date',
        'receipt_date',
        'total_amount',
        'tax_amount',
        'shipping_cost',
        'status', // 'pending', 'received', 'cancelled'
        'notes',
        'user_id', // Usuario que registró la compra
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'date',
        'receipt_date' => 'date:Y-m-d',
        'total_amount' => 'decimal:0',
        'tax_amount' => 'decimal:0',
        'shipping_cost' => 'decimal:0',
    ];
    
    /**
     * Get the provider that owns the purchase.
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
    
    /**
     * Get the user who created the purchase.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the items in the purchase.
     */
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
    
    /**
     * Get the inventory movements associated with this purchase.
     */
    public function inventoryMovements()
    {
        return $this->morphMany(InventoryMovement::class, 'reference');
    }
    
    /**
     * Get the total amount before tax.
     */
    public function getSubtotalAttribute()
    {
        return $this->total_amount - $this->tax_amount;
    }
    
    /**
     * Get the formatted total amount.
     */
    public function getFormattedTotalAttribute()
    {
        return '$ ' . number_format($this->total_amount, 0, ',', '.');
    }
    
    /**
     * Get the formatted status.
     */
    public function getFormattedStatusAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'Pendiente';
            case 'received':
                return 'Recibido';
            case 'cancelled':
                return 'Cancelado';
            default:
                return $this->status;
        }
    }
}
