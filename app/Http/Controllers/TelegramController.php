<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
