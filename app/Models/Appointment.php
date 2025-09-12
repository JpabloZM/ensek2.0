<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_request_id',
        'technician_id',
        'date',
        'start_time',
        'end_time',
        'notes',
        'status',
        'confirmation_status'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time->format('h:i A');
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time->format('h:i A');
    }

    public function getDurationAttribute()
    {
        return Carbon::parse($this->start_time)->diffInMinutes(Carbon::parse($this->end_time));
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'scheduled' => 'bg-primary',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            'rescheduled' => 'bg-warning',
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    public function getConfirmationStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-warning',
            'confirmed' => 'bg-success',
            'declined' => 'bg-danger',
        ];

        return $badges[$this->confirmation_status] ?? 'bg-warning';
    }

    public function isUpcoming()
    {
        $today = Carbon::today();
        return $this->date->greaterThanOrEqualTo($today) && $this->status == 'scheduled';
    }

    public function isPastDue()
    {
        $today = Carbon::today();
        return $this->date->lessThan($today) && $this->status == 'scheduled';
    }

    public function getDayNameAttribute()
    {
        return $this->date->locale('es')->isoFormat('dddd');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', Carbon::today())
                    ->where('status', 'scheduled');
    }

    public function scopeForTechnician($query, $technicianId)
    {
        return $query->where('technician_id', $technicianId);
    }

    public function scopeByServiceRequest($query, $serviceRequestId)
    {
        return $query->where('service_request_id', $serviceRequestId);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->whereHas('serviceRequest', function($q) use ($clientId) {
            $q->where('client_id', $clientId);
        });
    }

    public function scopeWithFilters($query, $filters)
    {
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }
        
        if (isset($filters['technician_id']) && $filters['technician_id']) {
            $query->where('technician_id', $filters['technician_id']);
        }
        
        if (isset($filters['service_id']) && $filters['service_id']) {
            $query->whereHas('serviceRequest', function($q) use ($filters) {
                $q->where('service_id', $filters['service_id']);
            });
        }
        
        return $query;
    }
}