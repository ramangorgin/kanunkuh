<?php

/**
 * Profile model storing user personal and membership details.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

/**
 * Represents a user's profile data and related mutators.
 */
class Profile extends Model
{
    use HasFactory;

    protected $table = 'profiles';

    protected $fillable = [
        'user_id',
        'membership_id',
        'membership_type',
        'membership_start',
        'membership_expiry',
        'leave_date',
        'first_name',
        'last_name',
        'father_name',
        'id_number',
        'id_place',
        'birth_date',
        'national_id',
        'photo',
        'national_card',
        'marital_status',
        'emergency_phone',
        'referrer',
        'education',
        'job',
        'home_address',
        'work_address',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Normalize and store the birth date value.
     */
    public function setBirthDateAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['birth_date'] = null;
            return;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $this->attributes['birth_date'] = $value;
            return;
        }

        try {
            $value = str_replace(['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'], ['0','1','2','3','4','5','6','7','8','9'], $value);

            [$year, $month, $day] = explode('/', $value);
            $this->attributes['birth_date'] = (new \Morilog\Jalali\Jalalian($year, $month, $day))
                ->toCarbon()
                ->toDateString();
        } catch (\Exception $e) {
            $this->attributes['birth_date'] = null;
        }
    }

    /**
     * Store the profile photo path if present.
     */
    public function setPhotoAttribute($value)
    {
        if (!empty($value) && $value !== 'profiles/') {
            $this->attributes['photo'] = $value;
        }
    }

    /**
     * Store the national card path if present.
     */
    public function setNationalCardAttribute($value)
    {
        if (!empty($value) && $value !== 'profiles/') {
            $this->attributes['national_card'] = $value;
        }
    }


    /**
     * Get the user that owns this profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate the next membership id.
     */
    public static function generateMembershipId()
    {
        $lastId = self::max('membership_id');
        return $lastId ? $lastId + 1 : 1000;
    }

}
