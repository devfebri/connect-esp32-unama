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

    public function report()
    {
        return view('report');
    }

    public function index()
    {
        return SensorReading::orderByDesc('created_at')->limit(10)->get();
    }

    public function all(Request $request)
    {
        $query = SensorReading::orderByDesc('created_at');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = min((int) $request->get('per_page', 25), 100);

        return $query->paginate($perPage)->through(fn($r) => $r);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kelembaban_tanah_1' => ['nullable', 'text'],
            'kelembaban_tanah_2' => ['nullable', 'numeric'],
            'kelembaban_tanah_3' => ['nullable', 'numeric'],
            'suhu_udara_1' => ['nullable', 'numeric'],
            'suhu_udara_2' => ['nullable', 'numeric'],
            'suhu_udara_3' => ['nullable', 'numeric'],
            'kelembaban_udara_1' => ['nullable', 'numeric'],
            'kelembaban_udara_2' => ['nullable', 'numeric'],
            'kelembaban_udara_3' => ['nullable', 'numeric'],
            'tekanan_udara_1' => ['nullable', 'numeric'],
            'tekanan_udara_2' => ['nullable', 'numeric'],
            'tekanan_udara_3' => ['nullable', 'numeric'],
            'curah_hujan' => ['nullable', 'numeric'],
        ]);

        if (empty(array_filter($data, fn($value) => ! is_null($value)))) {
            return response()->json([
                'success' => false,
                'message' => 'Payload must include at least one sensor value.',
            ], 422);
        }

        $reading = SensorReading::create($data);

        return response()->json([
            'success' => true,
            'reading' => $data,
        ]);
    }
}
