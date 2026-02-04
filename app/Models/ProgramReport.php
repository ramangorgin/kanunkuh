<?php

/**
 * Program report model storing structured report data.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a detailed report for a program.
 */
class ProgramReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'report_date',
        'reporter_id',
        'reporter_name',
        'leader_id',
        'leader_name',
        'report_program_type',
        'report_program_name',
        'report_region_route',
        'report_start_date',
        'report_end_date',
        'report_duration',
        'technical_feature',
        'report_description',
        'important_notes',
        'map_author',
        'map_scale',
        'map_source',
        'technical_equipments',
        'route_difficulty',
        'slope',
        'rock_engagement',
        'ice_engagement',
        'avg_backpack_weight',
        'prerequisites',
        'vegetation',
        'wildlife',
        'weather',
        'wind_speed',
        'temperature',
        'local_language',
        'attractions',
        'food_supply',
        'start_altitude',
        'target_altitude',
        'start_location_name',
        'local_village_name',
        'local_guide_info',
        'shelters_info',
        'shelters',
        'distance_from_tehran',
        'road_type',
        'transport_types',
        'facilities',
        'geo_points',
        'timeline',
        'participants',
        'participants_count',
    ];

    protected $casts = [
        'report_date' => 'datetime',
        'report_start_date' => 'date',
        'report_end_date' => 'date',
        'technical_equipments' => 'array',
        'transport_types' => 'array',
        'facilities' => 'array',
        'geo_points' => 'array',
        'timeline' => 'array',
        'participants' => 'array',
        'shelters' => 'array',
        'wind_speed' => 'integer',
        'temperature' => 'decimal:1',
        'avg_backpack_weight' => 'decimal:1',
        'start_altitude' => 'integer',
        'target_altitude' => 'integer',
        'distance_from_tehran' => 'integer',
        'participants_count' => 'integer',
    ];

    /**
     * Get the program associated with this report.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
    
    /**
     * Get the user who reported this program.
     */
    public function reporter()
    {
        return $this->belongsTo(\App\Models\User::class, 'reporter_id');
    }
    
    /**
     * Get the user who led the program.
     */
    public function leader()
    {
        return $this->belongsTo(\App\Models\User::class, 'leader_id');
    }
}

