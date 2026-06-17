<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    protected $fillable = [
        'temperature',
        'soil_temperature',
        'soil_moisture',
        'air_humidity',
        'air_pressure',
        'rainfall',
        'battery',
    ];

    protected $casts = [
        'temperature' => 'float',
        'soil_temperature' => 'float',
        'soil_moisture' => 'integer',
        'air_humidity' => 'float',
        'air_pressure' => 'float',
        'rainfall' => 'float',
        'battery' => 'float',
    ];
}
