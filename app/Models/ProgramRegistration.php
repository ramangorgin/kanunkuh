<?php

/**
 * Program registration model representing participant enrollments.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Program;
use App\Models\User;
use App\Models\Payment;

/**
 * Stores registration status, transport info, and payment reference.
 */
class ProgramRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'user_id',
        'payment_id',
        'guest_name',
        'guest_phone',
        'guest_national_id',
        'pickup_location',
        'needs_transport',
        'status',
    ];

    protected $casts = [
        'approved' => 'boolean',
    ];

    /**
     * Get the program associated with this registration.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
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
