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
        'specialization',
        'experience_years',
        'certification',
        'title',
        'bio',
        'status',
        'employment_type',
        'hire_date',
        'profile_image',
        'hourly_rate',
        'rating',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hire_date' => 'date',
        'hourly_rate' => 'decimal:2',
        'rating' => 'decimal:1',
        'experience_years' => 'integer',
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
        return $this->belongsTo(Service::class, 'specialty_id', 'id');
    }
    
    /**
     * Get the availability entries for the technician.
     */
    public function availability()
    {
        return $this->hasMany(TechnicianAvailability::class);
    }
    
    /**
     * Get the time off requests for the technician.
     */
    public function timeOffRequests()
    {
        return $this->hasMany(TechnicianTimeOff::class);
    }
    
    /**
     * Get the skills of the technician.
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'technician_skill')
                    ->withPivot('proficiency_level')
                    ->withTimestamps();
    }
}
