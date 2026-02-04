<?php

/**
 * Teacher model for course instructors.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents an instructor and associated course assignments.
 */
class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'profile_image',
        'birth_date',
        'biography',
        'skills',
        'certificates',
    ];

    /**
     * Get courses taught by this teacher.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
