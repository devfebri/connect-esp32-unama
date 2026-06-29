<?php

namespace App\Services;

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
            '— <i>Sistem Monitoring Sensor Lingkungan UNAMA</i>',
        ]);

        return $this->sendMessage($message);
    }
}
