<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
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
        'category',
    ];

    /**
     * Get the technicians that have this skill.
     */
    public function technicians()
    {
        return $this->belongsToMany(Technician::class, 'technician_skill')
                    ->withPivot('proficiency_level')
                    ->withTimestamps();
    }
}
