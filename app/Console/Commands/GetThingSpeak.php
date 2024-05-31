<?php

namespace App\Console\Commands;

use App\Events\CardEvent;
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

            // Broadcast data baru
            $newData = [
                'waktu_monitor' => $dataThingSpeak->waktu_monitor->format('Y-m-d H:i:s'),
                'suhu_monitor' => $suhu,
                'kelembaban_monitor' => $kelembaban
            ];
            broadcast(new ThingSpeakEvent($newData));

            // Mengambil data suhu lama
            $suhuSebelumnya = Monitor::orderBy('waktu_monitor', 'desc')->skip(1)->take(1)->pluck('suhu_monitor')->first();
            $kelembabanSebelumnya = Monitor::orderBy('waktu_monitor', 'desc')->skip(1)->take(1)->pluck('kelembaban_monitor')->first();

            // Broadcast data lama
            $lastData = [
                'suhu_sebelumnya' => $suhuSebelumnya,
                'kelembaban_sebelumnya' => $kelembabanSebelumnya,
            ];
            broadcast(new CardEvent($lastData));
            
            $this->info('Data berhasil disimpan : Suhu (' . $suhu . ') & Kelembaban : (' . $kelembaban . ')');
        } else {
            $this->error('Tidak ada data yang ditemukan');
        }
    }
}
