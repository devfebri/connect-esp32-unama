<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function __construct(private TelegramService $telegram) {}

    /**
     * Terima notifikasi dari frontend bahwa alat offline,
     * lalu kirim pesan peringatan ke grup Telegram.
     */
    public function notifyOffline(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'since' => ['required', 'string', 'max:100'],
            'duration' => ['required', 'string', 'max:100'],
        ]);

        $sent = $this->telegram->sendOfflineAlert(
            sinceFormatted: $validated['since'],
            durationFormatted: $validated['duration'],
        );

        if ($sent) {
            return response()->json(['status' => 'ok', 'message' => 'Notifikasi Telegram terkirim.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Gagal mengirim notifikasi Telegram.'], 500);
    }

    /**
     * Webhook endpoint yang menerima update dari Telegram Bot API.
     * Memproses perintah /status untuk mengirim data sensor terbaru.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $update = $request->all();

        Log::info('TelegramWebhook: update diterima.', ['update' => $update]);

        $message = $update['message'] ?? $update['channel_post'] ?? null;

        if (! $message) {
            return response()->json(['ok' => true]);
        }

        $chatId = $message['chat']['id'] ?? null;
        $text = trim($message['text'] ?? '');

        if (! $chatId) {
            return response()->json(['ok' => true]);
        }

        // Tangani hanya perintah /status (termasuk /status@NamaBotAnda)
        if (str_starts_with($text, '/status')) {
            $this->telegram->sendStatusSensor($chatId);
        }

        return response()->json(['ok' => true]);
    }
}
