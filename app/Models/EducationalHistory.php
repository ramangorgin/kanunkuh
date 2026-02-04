<?php

/**
 * Educational history model for user training records.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

/**
 * Stores prior education entries and related certificate data.
 */
class EducationalHistory extends Model
{
    use HasFactory;

    protected $table = 'educational_histories';

    protected $fillable = [
        'user_id',
        'federation_course_id',
        'custom_course_title',
        'issue_date', 
        'certificate_file',
    ];

    /**
     * Get the user that owns this history record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the federation course referenced by this history record.
     */
    public function federationCourse()
    {
        return $this->belongsTo(FederationCourse::class, 'federation_course_id');
    }

    /**
     * Accessor for Jalali-formatted issue date.
     */
    public function getIssueDateJalaliAttribute()
    {
        return $this->issue_date
            ? Jalalian::fromCarbon(Carbon::parse($this->issue_date))->format('Y/m/d')
            : null;
    }
}
