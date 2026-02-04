<?php

/**
 * Program user role model linking users to program roles.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Stores role assignments for users participating in programs.
 */
class ProgramUserRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'user_id',
        'user_name',
        'role_title',
    ];


    /**
     * Get the program associated with this role.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the user assigned to this role.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
