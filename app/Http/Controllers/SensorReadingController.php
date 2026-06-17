<?php

namespace App\Http\Controllers;

use App\Models\SensorReading;
use Illuminate\Http\Request;

class SensorReadingController extends Controller
{
    public function dashboard()
    {
        return view('sensors');
    }

    public function index()
    {
        return SensorReading::orderByDesc('created_at')->limit(10)->get();
    }

    public function store(Request $request)
    {
        dd($request->all());
        $data = $request->validate([
            'temperature' => ['nullable', 'numeric'],
            'soil_temperature' => ['nullable', 'numeric'],
            'soil_moisture' => ['nullable', 'integer', 'between:0,100'],
            'air_humidity' => ['nullable', 'numeric', 'between:0,100'],
            'battery' => ['nullable', 'numeric'],
        ]);

        $reading = SensorReading::create($data);

        return response()->json([
            'success' => true,
            'reading' => $reading,
        ]);
    }
}
