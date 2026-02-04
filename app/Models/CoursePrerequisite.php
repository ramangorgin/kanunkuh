<?php

/**
 * Model linking federation courses to their prerequisites.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a prerequisite relationship between federation courses.
 */
class CoursePrerequisite extends Model
{
    use HasFactory;

    protected $table = 'course_prerequisites';

    protected $fillable = [
        'course_id',
        'prerequisite_id',  
    ];

    /**
     * Get the federation course that requires a prerequisite.
     */
    public function course()
    {
        return $this->belongsTo(FederationCourse::class, 'course_id');
    }

    /**
     * Get the prerequisite federation course.
     */
    public function prerequisite()
    {
        return $this->belongsTo(FederationCourse::class, 'prerequisite_id');
    }
}

