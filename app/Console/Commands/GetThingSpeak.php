<?php

namespace App\Console\Commands;

use App\Events\ThingSpeakEvent;
use App\Models\Monitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetThingSpeak extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-thing-speak';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get latest data from ThingSpeak API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $channelId = '2476613';
        $apiKey = 'OB202AUVGT70OMR2';
        $field1 = 'field1';
        $field2 = 'field2';

        $response = Http::get("https://api.thingspeak.com/channels/{$channelId}/feeds.json", [
            'api_key' => $apiKey,
            'results' => 1
        ]);

        $data = $response->json();

        if (!empty($data['feeds'])) {
            $latestData = $data['feeds'][0];

            // Simpan data ke dalam database
            $dataThingSpeak = new Monitor();
            $dataThingSpeak->waktu_monitor = now();
            $dataThingSpeak->suhu_monitor = $latestData[$field1];
            $dataThingSpeak->kelembaban_monitor = $latestData[$field2];
            $dataThingSpeak->save();

            $suhu = $latestData[$field1];
            $kelembaban = $latestData[$field2];

            // Kumpulkan data dalam sebuah array
            $newData = [
                'waktu_monitor' => $dataThingSpeak->waktu_monitor->format('Y-m-d H:i:s'), // Format sesuai yang diharapkan oleh DataTables
                'suhu_monitor' => $suhu,
                'kelembaban_monitor' => $kelembaban
            ];

            // Emitkan event 'thingspeak-event' dengan data baru
            broadcast(new ThingSpeakEvent($newData));
            
            $this->info('Data berhasil disimpan : Suhu (' . $suhu . ') & Kelembaban : (' . $kelembaban . ')');
        } else {
            $this->error('Tidak ada data yang ditemukan');
        }
    }
}
