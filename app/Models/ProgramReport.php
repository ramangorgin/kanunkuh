<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
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
        'technical_equipments' => 'array',
        'transport_types' => 'array',
        'facilities' => 'array',
        'geo_points' => 'array',
        'timeline' => 'array',
        'participants' => 'array',
        'wind_speed' => 'integer',
        'temperature' => 'decimal:1',
        'avg_backpack_weight' => 'decimal:1',
        'start_altitude' => 'integer',
        'target_altitude' => 'integer',
        'distance_from_tehran' => 'integer',
        'participants_count' => 'integer',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}

