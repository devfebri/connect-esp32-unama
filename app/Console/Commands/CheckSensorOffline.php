<?php

namespace App\Console\Commands;

use App\Models\SensorReading;
use App\Services\TelegramService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

#[Signature('sensor:check-offline')]
#[Description('Cek apakah sensor offline dan kirim notifikasi Telegram jika perlu.')]
class CheckSensorOffline extends Command
{
    /**
     * Ambang batas offline: data tidak masuk lebih dari 5 menit = offline.
     */
    private const OFFLINE_THRESHOLD_MINUTES = 5;

    /**
     * Cooldown antar notifikasi: jangan kirim lagi dalam 60 menit (1 jam).
     */
    private const NOTIFY_COOLDOWN_MINUTES = 60;

    private const CACHE_KEY = 'telegram_offline_notified_at';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegram): int
    {
        $latest = SensorReading::latest('waktu_pembacaan')->first();

        if (! $latest) {
            $this->warn('Belum ada data sensor sama sekali di database.');

            return self::SUCCESS;
        }

        $lastTime = Carbon::parse($latest->waktu_pembacaan ?? $latest->created_at);
        $diffMinutes = $lastTime->diffInMinutes(now());

        if ($diffMinutes < self::OFFLINE_THRESHOLD_MINUTES) {
            $this->info("Sensor online. Data terakhir {$diffMinutes} menit lalu.");

            // Reset cache notifikasi saat sensor kembali online
            Cache::forget(self::CACHE_KEY);

            return self::SUCCESS;
        }

        // Sensor offline — cek apakah sudah kirim notifikasi belum
        $lastNotifiedAt = Cache::get(self::CACHE_KEY);

        if ($lastNotifiedAt && now()->diffInMinutes($lastNotifiedAt) < self::NOTIFY_COOLDOWN_MINUTES) {
            $remainingMinutes = self::NOTIFY_COOLDOWN_MINUTES - now()->diffInMinutes($lastNotifiedAt);
            $this->line("Sensor offline, tapi notifikasi sudah dikirim. Cooldown tersisa {$remainingMinutes} menit.");

            return self::SUCCESS;
        }

        // Kirim notifikasi Telegram
        $sinceFormatted = $lastTime->locale('id')->isoFormat('D MMM YYYY, HH:mm');
        $durationFormatted = $lastTime->locale('id')->diffForHumans(now(), ['parts' => 2]);

        $sent = $telegram->sendOfflineAlert($sinceFormatted, $durationFormatted);

        if ($sent) {
            Cache::put(self::CACHE_KEY, now(), now()->addMinutes(self::NOTIFY_COOLDOWN_MINUTES + 10));
            $this->info("✅ Notifikasi Telegram terkirim. Sensor offline sejak {$sinceFormatted} ({$durationFormatted}).");
        } else {
            $this->error('❌ Gagal mengirim notifikasi Telegram. Cek log untuk detail.');
        }

        return self::SUCCESS;
    }
}
