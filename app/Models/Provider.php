<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'tax_id',
        'website',
        'payment_terms',
        'notes',
        'is_active',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    /**
     * Get all purchases from this provider.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    
    /**
     * Get inventory movements associated with this provider.
     */
    public function inventoryMovements()
    {
        return $this->morphMany(InventoryMovement::class, 'reference');
    }
    
    /**
     * Get items provided by this provider
     */
    public function items()
    {
        return $this->belongsToMany(InventoryItem::class, 'provider_inventory_item')
            ->withPivot(['provider_code', 'provider_price', 'lead_time'])
            ->withTimestamps();
    }
}
