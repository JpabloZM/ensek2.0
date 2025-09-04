<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'specialty',
        'skills',
        'active',
        'availability',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
    ];
    
    /**
     * Get the user that owns the technician profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the schedules for the technician.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    
    /**
     * Get the specialty (service) of the technician.
     */
    public function specialtyService()
    {
        return $this->belongsTo(Service::class, 'specialty', 'id');
    }
}
