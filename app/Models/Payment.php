<?php

/**
 * Payment model for tracking user transactions.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Program;
use App\Models\Course;
use App\Models\User;

/**
 * Stores payment details and related entity references.
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'amount', 
        'type', 
        'year', 
        'related_id', 
        'status',
        'membership_code', 
        'transaction_code',
        'metadata',
    ];


    protected $casts = [
        'payment_date' => 'date',
        'approved' => 'boolean',
    ];

    /**
     * Get the user that owns this payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the program registration associated with this payment.
     */
    public function registration()
    {
        return $this->hasOne(ProgramRegistration::class);
    }

    /**
     * Get the related program if the payment is for a program.
     */
    public function program()
    {
        return $this->belongsTo(Program::class, 'related_id');
    }

    /**
     * Get the related course if the payment is for a course.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'related_id');
    }
}