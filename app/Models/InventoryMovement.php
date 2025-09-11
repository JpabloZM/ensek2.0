<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inventory_item_id',
        'type', // 'add', 'remove', 'adjust'
        'quantity',
        'notes',
        'user_id', // Usuario que realizó el movimiento
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'previous_quantity' => 'integer',
        'new_quantity' => 'integer',
        'unit_price' => 'decimal:0',
    ];
    
    /**
     * Get the inventory item of the movement.
     */
    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
    
    /**
     * Get the user who created the movement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the reference model (polymorphic relationship)
     */
    public function reference()
    {
        return $this->morphTo();
    }
    
    /**
     * Get the formatted value of the movement.
     */
    public function getValueAttribute()
    {
        return '$' . number_format($this->quantity * $this->unit_price, 2);
    }
    
    /**
     * Scope for entry movements.
     */
    public function scopeEntries($query)
    {
        return $query->where('type', 'add');
    }
    
    /**
     * Scope for exit movements.
     */
    public function scopeExits($query)
    {
        return $query->where('type', 'remove');
    }
    
    /**
     * Scope for adjustments.
     */
    public function scopeAdjustments($query)
    {
        return $query->where('type', 'adjust');
    }
    
    /**
     * Format the movement type for display.
     */
    public function getFormattedTypeAttribute()
    {
        switch ($this->type) {
            case 'add':
                return 'Entrada';
            case 'remove':
                return 'Salida';
            case 'adjust':
                return 'Ajuste';
            default:
                return $this->type;
        }
    }
}
