<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->float('sensor_tanah_adc_1')->nullable();
            $table->float('kelembaban_tanah_1')->nullable()->comment('satuannya %');
            $table->float('sensor_tanah_adc_2')->nullable();
            $table->float('kelembaban_tanah_2')->nullable()->comment('satuannya %');
            $table->float('sensor_tanah_adc_3')->nullable();
            $table->float('kelembaban_tanah_3')->nullable()->comment('satuannya %');
            $table->float('kelembaban_udara_1')->nullable();
            $table->float('kelembaban_udara_2')->nullable();
            $table->float('kelembaban_udara_3')->nullable();
            $table->float('suhu_udara_1')->nullable();
            $table->float('suhu_udara_2')->nullable();
            $table->float('suhu_udara_3')->nullable();
            $table->float('tekanan_udara_1')->nullable();
            $table->float('tekanan_udara_2')->nullable();
            $table->float('tekanan_udara_3')->nullable();
            $table->float('curah_hujan')->nullable();
            $table->dateTime('waktu_pembacaan', 0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
