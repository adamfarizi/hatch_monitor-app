<?php

namespace App\Console;

use App\Models\Monitor;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Http\Controllers\KontrolAlatController;
use Illuminate\Support\Facades\Http;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(
            function () {
                $channelId = '2476613';
                $apiKey = 'OB202AUVGT70OMR2';
                $field1 = 'field1';
                $field2 = 'field2';

                $response = Http::get("https://api.thingspeak.com/channels/{$channelId}/feeds.json", [
                    'api_key' => $apiKey,
                    'results' => 1
                ]);

                $data = $response->json();

                if (!empty ($data['feeds'])) {
                    $latestData = $data['feeds'][0];

                    // Simpan data ke dalam database
                    $dataThingSpeak = new Monitor();
                    $dataThingSpeak->waktu_monitor = now();
                    $dataThingSpeak->suhu_monitor = $latestData[$field1];
                    $dataThingSpeak->kelembaban_monitor = $latestData[$field2];
                    $dataThingSpeak->save();

                    return response()->json(['message' => 'Data berhasil disimpan'], 200);
                } else {
                    return response()->json(['error' => 'Tidak ada data yang ditemukan'], 404);
                }
            }
        )->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
