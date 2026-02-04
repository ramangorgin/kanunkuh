<?php

/**
 * Course registration model representing member and guest enrollments.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Stores registration status, payment link, and certificate references.
 */
class CourseRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'payment_id',
        'guest_name',
        'guest_phone',
        'guest_national_id',
        'status',
        'approved',
        'certificate_file',
    ];

    protected $casts = [
        'approved' => 'boolean',
    ];
    
    /**
     * Get the course associated with this registration.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user associated with this registration.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment associated with this registration.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
