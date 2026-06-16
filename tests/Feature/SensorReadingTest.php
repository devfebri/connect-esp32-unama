<?php

namespace Tests\Feature;

use App\Models\SensorReading;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SensorReadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitor_page_is_accessible(): void
    {
        $response = $this->get('/monitor');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Sensor');
    }

    public function test_esp32_reading_can_be_stored_and_returned(): void
    {
        $payload = [
            'temperature' => 24.5,
            'soil_temperature' => 20.9,
            'soil_moisture' => 74,
            'air_humidity' => 60.2,
            'battery' => 3.85,
        ];

        $response = $this->postJson('/api/esp32/readings', $payload);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'reading' => ['temperature' => 24.5, 'soil_moisture' => 74]]);

        $this->assertDatabaseHas('sensor_readings', [
            'temperature' => 24.5,
            'soil_moisture' => 74,
            'battery' => 3.85,
        ]);

        $listResponse = $this->getJson('/api/esp32/readings');
        $listResponse->assertStatus(200);
        $this->assertCount(1, $listResponse->json());
    }
}
