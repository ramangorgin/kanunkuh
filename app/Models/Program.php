<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'peak_height',
        'program_type',
        'region_name',
        'execution_date',
        'move_from_karaj',
        'move_from_tehran',
        'cost_member',
        'cost_guest',
        'payment_info',
        'equipments',
        'meals',
        'conditions',
        'register_deadline',
        'rules',
        'status',
    ];

    protected $casts = [
        'execution_date' => 'datetime',
        'register_deadline' => 'datetime',
        'payment_info' => 'array',
        'equipments' => 'array',
        'meals' => 'array',
        'conditions' => 'array',
    ];

    public function registrations()
    {
        return $this->hasMany(ProgramRegistration::class);
    }

    public function files()
    {
        return $this->hasMany(ProgramFile::class);
    }

    public function userRoles()
    {
        return $this->hasMany(ProgramUserRole::class);
    }

    public function report()
    {
        return $this->hasOne(ProgramReport::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'related_id')->where('type', 'program');
    }
    
}