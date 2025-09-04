<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'user_id',
        'client_name',
        'client_phone',
        'client_email',
        'description',
        'address',
        'status',
        'notes',
    ];
    
    /**
     * Get the service that owns the service request.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    
    /**
     * Get the user that created the service request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the schedule associated with the service request.
     */
    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }
}
