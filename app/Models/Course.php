<?php

/**
 * Course model representing training courses and related metadata.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Encapsulates course attributes, relationships, and prerequisite checks.
 */
class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'federation_course_id',
        'title',
        'description',
        'teacher_id',
        'code',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'place',
        'place_address',
        'place_lat',
        'place_lon',
        'capacity',
        'is_free',
        'member_cost',
        'guest_cost',
        'card_number',
        'sheba_number',
        'card_holder',
        'bank_name',
        'is_registration_open',
        'registration_deadline',
        'report',
        'status',
        'is_special',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_deadline' => 'datetime',
        'is_free' => 'boolean',
        'is_registration_open' => 'boolean',
        'is_special' => 'boolean',
    ];

    /**
     * Get the assigned instructor for the course.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the associated federation course reference.
     */
    public function federationCourse()
    {
        return $this->belongsTo(FederationCourse::class, 'federation_course_id');
    }

    /**
     * Get registrations for this course.
     */
    public function registrations()
    {
        return $this->hasMany(CourseRegistration::class);
    }

    /**
     * Get attached files for this course.
     */
    public function files()
    {
        return $this->hasMany(CourseFile::class);
    }

    /**
     * Get prerequisite federation courses for this course.
     */
    public function prerequisites()
    {
        if (!$this->federation_course_id) {
            return collect();
        }
        
        return CoursePrerequisite::where('course_id', $this->federation_course_id)
            ->with('prerequisite')
            ->get()
            ->pluck('prerequisite');
    }

    /**
        * Determine whether a user has completed all prerequisites.
     */
    public function userHasCompletedPrerequisites($userId)
    {
        if (!$this->federation_course_id) {
            return true; // No prerequisites for non-federation courses
        }

        $prerequisites = CoursePrerequisite::where('course_id', $this->federation_course_id)
            ->pluck('prerequisite_id');

        if ($prerequisites->isEmpty()) {
            return true;
        }

        $completedCourses = \App\Models\EducationalHistory::where('user_id', $userId)
            ->whereIn('federation_course_id', $prerequisites)
            ->pluck('federation_course_id');

        return $prerequisites->diff($completedCourses)->isEmpty();
    }

}