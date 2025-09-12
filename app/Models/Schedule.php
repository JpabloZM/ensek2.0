<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_request_id',
        'technician_id',
        'scheduled_date',
        'duration',
        'estimated_end_time',
        'status',
        'confirmation_status',
        'notes',
        'completed_at',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_date' => 'datetime',
        'estimated_end_time' => 'datetime',
        'completed_at' => 'datetime',
        'duration' => 'integer',
    ];
    
    /**
     * Get the service request that owns the schedule.
     */
    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
    
    /**
     * Get the technician assigned to the schedule.
     */
    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }
}
