<?php

namespace App\Services;

use App\Models\SensorReading;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $botToken;

    private string $chatId;

    public function __construct()
    {
        $this->botToken = config('telegram.bot_token', '');
        $this->chatId = config('telegram.chat_id', '');
    }

    /**
     * Kirim pesan teks ke grup Telegram.
     *
     * @param  string  $message  Teks pesan (mendukung HTML parse mode)
     */
    public function sendMessage(string $message): bool
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            Log::warning('TelegramService: Bot token atau Chat ID belum dikonfigurasi.');

            return false;
        }

        try {
            $response = Http::timeout(10)->post(
                "https://api.telegram.org/bot{$this->botToken}/sendMessage",
                [
                    'chat_id' => $this->chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]
            );

            if (! $response->successful()) {
                Log::error('TelegramService: Gagal mengirim pesan.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('TelegramService: Exception saat mengirim pesan.', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Kirim notifikasi alat offline ke grup Telegram.
     *
     * @param  string  $sinceFormatted  Waktu terakhir data masuk (format teks)
     * @param  string  $durationFormatted  Durasi offline (format teks)
     */
    public function sendOfflineAlert(string $sinceFormatted, string $durationFormatted): bool
    {
        $message = implode("\n", [
            '🚨 <b>PERINGATAN — ALAT SENSOR OFFLINE</b>',
            '',
            '📡 Sistem monitoring ESP32 UNAMA mendeteksi bahwa alat sensor tidak mengirim data.',
            '',
            '⏰ <b>Terakhir aktif :</b> '.$sinceFormatted,
            '⌛ <b>Sudah offline :</b> '.$durationFormatted,
            '',
            '⚠️ <b>Kemungkinan penyebab:</b>',
            '  • Perangkat ESP32 mati atau tidak merespons',
            '  • Koneksi internet/WiFi terputus di lokasi alat',
            '  • Gangguan pada catu daya perangkat',
            '',
            '🔧 Segera periksa kondisi alat di lapangan.',
            '',
            '— <i>Sistem Monitoring Sensor Lingkungan Perkebunan Nanas</i>',
        ]);

        return $this->sendMessage($message);
    }

    /**
     * Kirim data status sensor terbaru ke chat Telegram tertentu.
     *
     * @param  int|string  $chatId  ID chat tujuan (bisa berbeda dengan default group)
     */
    public function sendStatusSensor(int|string $chatId): bool
    {
        $latest = SensorReading::latest('waktu_pembacaan')->first();

        if (! $latest) {
            return $this->sendMessageTo($chatId, '⚠️ <b>Tidak ada data sensor yang tersedia.</b>');
        }

        $waktu = $latest->waktu_pembacaan
            ? Carbon::parse($latest->waktu_pembacaan)->timezone('Asia/Makassar')->format('d M Y, H:i:s').' WITA'
            : '-';

        $message = implode("\n", [
            '📊 <b>STATUS SENSOR — MONITORING ESP32 UNAMA</b>',
            '',
            '🕐 <b>Waktu pembacaan :</b> '.$waktu,
            '',
            '🌱 <b>KELEMBABAN TANAH</b>',
            '  • Sensor 1 : '.($latest->kelembaban_tanah_1 !== null ? round($latest->kelembaban_tanah_1, 1).' %' : '-'),
            '  • Sensor 2 : '.($latest->kelembaban_tanah_2 !== null ? round($latest->kelembaban_tanah_2, 1).' %' : '-'),
            '  • Sensor 3 : '.($latest->kelembaban_tanah_3 !== null ? round($latest->kelembaban_tanah_3, 1).' %' : '-'),
            '',
            '💧 <b>KELEMBABAN UDARA</b>',
            '  • Node 1 : '.($latest->kelembaban_udara_1 !== null ? round($latest->kelembaban_udara_1, 1).' %' : '-'),
            '  • Node 2 : '.($latest->kelembaban_udara_2 !== null ? round($latest->kelembaban_udara_2, 1).' %' : '-'),
            '  • Node 3 : '.($latest->kelembaban_udara_3 !== null ? round($latest->kelembaban_udara_3, 1).' %' : '-'),
            '',
            '🌡️ <b>SUHU UDARA</b>',
            '  • Node 1 : '.($latest->suhu_udara_1 !== null ? round($latest->suhu_udara_1, 1).' °C' : '-'),
            '  • Node 2 : '.($latest->suhu_udara_2 !== null ? round($latest->suhu_udara_2, 1).' °C' : '-'),
            '  • Node 3 : '.($latest->suhu_udara_3 !== null ? round($latest->suhu_udara_3, 1).' °C' : '-'),
            '',
            '🔵 <b>TEKANAN UDARA</b>',
            '  • Node 1 : '.($latest->tekanan_udara_1 !== null ? round($latest->tekanan_udara_1, 1).' hPa' : '-'),
            '  • Node 2 : '.($latest->tekanan_udara_2 !== null ? round($latest->tekanan_udara_2, 1).' hPa' : '-'),
            '  • Node 3 : '.($latest->tekanan_udara_3 !== null ? round($latest->tekanan_udara_3, 1).' hPa' : '-'),
            '',
            '🌧️ <b>CURAH HUJAN</b>',
            '  • Total   : '.($latest->curah_hujan !== null ? round($latest->curah_hujan, 1).' mm' : '-'),
            '',
            '— <i>Sistem Monitoring Sensor Lingkungan Perkebunan Nanas</i>',
        ]);

        return $this->sendMessageTo($chatId, $message);
    }

    /**
     * Kirim pesan ke chat ID tertentu (bukan hanya default group).
     *
     * @param  int|string  $chatId  Target chat ID
     * @param  string  $message  Teks pesan (mendukung HTML parse mode)
     */
    public function sendMessageTo(int|string $chatId, string $message): bool
    {
        if (empty($this->botToken)) {
            Log::warning('TelegramService: Bot token belum dikonfigurasi.');

            return false;
        }

        try {
            $response = Http::timeout(10)->post(
                "https://api.telegram.org/bot{$this->botToken}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]
            );

            if (! $response->successful()) {
                Log::error('TelegramService: Gagal mengirim pesan ke chat.', [
                    'chat_id' => $chatId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('TelegramService: Exception saat mengirim pesan ke chat.', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
