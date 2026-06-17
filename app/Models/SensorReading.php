<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    protected $fillable = [
        'kelembaban_tanah_1',
        'kelembaban_tanah_2',
        'kelembaban_tanah_3',
        'suhu_udara_1',
        'suhu_udara_2',
        'suhu_udara_3',
        'kelembaban_udara_1',
        'kelembaban_udara_2',
        'kelembaban_udara_3',
        'tekanan_udara_1',
        'tekanan_udara_2',
        'tekanan_udara_3',
        'curah_hujan',
    ];

    protected $casts = [
        'kelembaban_tanah_1' => 'float',
        'kelembaban_tanah_2' => 'float',
        'kelembaban_tanah_3' => 'float',
        'suhu_udara_1' => 'float',
        'suhu_udara_2' => 'float',
        'suhu_udara_3' => 'float',
        'kelembaban_udara_1' => 'float',
        'kelembaban_udara_2' => 'float',
        'kelembaban_udara_3' => 'float',
        'tekanan_udara_1' => 'float',
        'tekanan_udara_2' => 'float',
        'tekanan_udara_3' => 'float',
        'curah_hujan' => 'float',
        'soil_moisture' => 'integer',
        'air_humidity' => 'float',
        'air_pressure' => 'float',
        'rainfall' => 'float',
    ];
}
