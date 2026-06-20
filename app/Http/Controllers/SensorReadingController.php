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

        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sensor_tanah_adc_1' => ['nullable', 'numeric'],
            'kelembaban_tanah_1' => ['nullable', 'numeric'],
            'sensor_tanah_adc_2' => ['nullable', 'numeric'],
            'kelembaban_tanah_2' => ['nullable', 'numeric'],
            'sensor_tanah_adc_3' => ['nullable', 'numeric'],
            'kelembaban_tanah_3' => ['nullable', 'numeric'],
            'kelembaban_udara_1' => ['nullable', 'numeric'],
            'kelembaban_udara_2' => ['nullable', 'numeric'],
            'kelembaban_udara_3' => ['nullable', 'numeric'],
            'suhu_udara_1' => ['nullable', 'numeric'],
            'suhu_udara_2' => ['nullable', 'numeric'],
            'suhu_udara_3' => ['nullable', 'numeric'],
            'tekanan_udara_1' => ['nullable', 'numeric'],
            'tekanan_udara_2' => ['nullable', 'numeric'],
            'tekanan_udara_3' => ['nullable', 'numeric'],
            'curah_hujan' => ['nullable', 'numeric'],
            'created_at' => ['nullable', 'date'],
        ]);

        if (empty(array_filter($data, fn($value) => ! is_null($value)))) {
            return response()->json([
                'success' => false,
                'message' => 'Payload must include at least one sensor value.',
            ], 422);
        }

        SensorReading::create($data);

        return response()->json([
            'success' => true,
            'reading' => $data,
        ]);
    }
    public function import(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json([
                'status' => false,
                'message' => 'File tidak ditemukan'
            ], 400);
        }

        $file = $request->file('file');

        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return response()->json([
                'status' => false,
                'message' => 'File gagal dibaca'
            ], 400);
        }

        $header = fgetcsv($handle);

        $data = [];

        while (($row = fgetcsv($handle)) !== false) {
            $sensor_tanah_adc_1 = $row[0];
            $kelembaban_tanah_1 = $row[1];
            $sensor_tanah_adc_2 = $row[2];
            $kelembaban_tanah_2 = $row[3];
            $sensor_tanah_adc_3 = $row[4];
            $kelembaban_tanah_3 = $row[5];
            $kelembaban_udara_1 = $row[6];
            $kelembaban_udara_2 = $row[7];
            $kelembaban_udara_3 = $row[8];
            $suhu_udara_1 = $row[9];
            $suhu_udara_2 = $row[10];
            $suhu_udara_3 = $row[11];
            $tekanan_udara_1 = $row[12];
            $tekanan_udara_2 = $row[13];
            $tekanan_udara_3 = $row[14];
            $curah_hujan = $row[15];
            $created_at = $row[16];

            $data[] = [
                'sensor_tanah_adc_1' => $sensor_tanah_adc_1,
                'kelembaban_tanah_1' => $kelembaban_tanah_1,
                'sensor_tanah_adc_2' => $sensor_tanah_adc_2,
                'kelembaban_tanah_2' => $kelembaban_tanah_2,
                'sensor_tanah_adc_3' => $sensor_tanah_adc_3,
                'kelembaban_tanah_3' => $kelembaban_tanah_3,
                'kelembaban_udara_1' => $kelembaban_udara_1,
                'kelembaban_udara_2' => $kelembaban_udara_2,
                'kelembaban_udara_3' => $kelembaban_udara_3,
                'suhu_udara_1' => $suhu_udara_1,
                'suhu_udara_2' => $suhu_udara_2,
                'suhu_udara_3' => $suhu_udara_3,
                'tekanan_udara_1' => $tekanan_udara_1,
                'tekanan_udara_2' => $tekanan_udara_2,
                'tekanan_udara_3' => $tekanan_udara_3,
                'curah_hujan' => $curah_hujan,
                'created_at' => $created_at,
                'updated_at' => now(),
            ];
        }

        fclose($handle);

        if (count($data) > 0) {
            DB::table('sensor_logs')->insert($data);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diimport',
            'jumlah_data' => count($data)
        ]);
    }
}
