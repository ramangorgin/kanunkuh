<?php

/**
 * Course file model for storing course-related media metadata.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a file attached to a course.
 */
class CourseFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'file_type',
        'file_path',
        'caption',
    ];

    /**
     * Get the course this file belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
