<?php

/**
 * Federation course reference model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a federation course definition used across the system.
 */
class FederationCourse extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'title', 'description'];
    public $incrementing = false;
    protected $keyType = 'int';

    /**
     * Get prerequisite federation courses.
     */
    public function prerequisites()
    {
        return $this->belongsToMany(
            FederationCourse::class,
            'course_prerequisites',
            'course_id',
            'prerequisite_id'
        );
    }

    /**
     * Get educational history entries referencing this course.
     */
    public function histories()
    {
        return $this->hasMany(EducationalHistory::class);
    }
}
