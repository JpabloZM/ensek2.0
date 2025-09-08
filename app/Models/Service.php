<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'tax_rate',
        'duration',
        'special_requirements',
        'materials_included',
        'requires_technician_approval',
        'active',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'active' => 'boolean',
        'requires_technician_approval' => 'boolean',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * Get the service requests for the service.
     */
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
    
    /**
     * Get the price including tax.
     * 
     * @return float
     */
    public function getPriceWithTaxAttribute()
    {
        return $this->price * (1 + ($this->tax_rate / 100));
    }
    
    /**
     * Get formatted price with currency.
     * 
     * @return string
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }
    
    /**
     * Get formatted price with tax and currency.
     * 
     * @return string
     */
    public function getFormattedPriceWithTaxAttribute()
    {
        return '$' . number_format($this->price_with_tax, 2);
    }
}
