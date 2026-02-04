<?php

/**
 * User model representing application accounts.
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Ticket;

/**
 * Represents a user with profile, registration, and ticket relationships.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
    ];

    /**
     * Determine if the user has admin role.
     */
    public function Admin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check whether required registration steps are complete.
     */
    public function isRegistrationComplete()
    {
        return $this->profile 
            && $this->medicalRecord 
            && $this->educationalHistories()->exists();
    }

    /**
     * Get the full name derived from the profile.
     */
    public function getFullNameAttribute()
    {
        return optional($this->profile)->first_name . ' ' . optional($this->profile)->last_name;
    }

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the user's medical record.
     */
    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }

    /**
     * Get the user's educational histories.
     */
    public function educationalHistories()
    {
        return $this->hasMany(EducationalHistory::class);
    }

    /**
     * Get the user's payments.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
    /**
     * Get the user's program registrations.
     */
    public function programRegistrations()
    {
        return $this->hasMany(ProgramRegistration::class);
    }

    /**
     * Get the user's course registrations.
     */
    public function courseRegistrations()
    {
        return $this->hasMany(CourseRegistration::class);
    }

    /**
     * Get programs the user is registered for.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_registrations')
                    ->withPivot('approved', 'guest_name', 'guest_phone');
    }

    /**
     * Get courses the user is registered for.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_registrations')
                    ->withPivot('approved', 'guest_name', 'guest_phone');
    }

    /**
     * Get tickets created by the user.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Check whether the user has a profile.
     */
    public function hasProfile()
    {
        return $this->profile()->exists();
    }

    /**
     * Check whether the user has a medical record.
     */
    public function hasMedicalRecord()
    {
        return $this->medicalRecord()->exists();
    }

    /**
     * Check whether the user has educational history records.
     */
    public function hasEducationalHistory()
    {
        return $this->educationalHistories()->exists();
    }

}
