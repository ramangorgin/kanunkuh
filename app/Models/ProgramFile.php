<?php

/**
 * Program file model for storing program media metadata.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a file attached to a program.
 */
class ProgramFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'file_type',
        'file_path',
        'caption',
    ];

    /**
     * Get the program this file belongs to.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
