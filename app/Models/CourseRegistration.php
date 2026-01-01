<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'approved', // For backward compatibility
        'certificate_file',
    ];

    protected $casts = [
        'approved' => 'boolean',
    ];
    
    // For backward compatibility, keep approved field but also use status

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
