<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class KontrolAlatController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Kontrol Alat';

        $suhu = Monitor::orderBy('waktu_monitor', 'desc')->pluck('suhu_monitor')->first();
        $kelembaban = Monitor::orderBy('waktu_monitor', 'desc')->pluck('kelembaban_monitor')->first();

        $suhuSebelumnya = Monitor::orderBy('waktu_monitor', 'desc')->skip(1)->take(1)->pluck('suhu_monitor')->first();
        $kelembabanSebelumnya = Monitor::orderBy('waktu_monitor', 'desc')->skip(1)->take(1)->pluck('kelembaban_monitor')->first();

        if ($request->ajax()) {
            $filterBulan = $request->filterBulan;

            $data = Monitor::where('waktu_monitor', 'like', $filterBulan . '%')
                ->orderByDesc('waktu_monitor')
                ->get();
            return DataTables::of($data)
                ->make(true);
        }

        //* Mengambil status relay 
        try {
            $channelId = '2476613';
            $apiKey = 'OB202AUVGT70OMR2';
            $response = Http::get("https://api.thingspeak.com/channels/{$channelId}/feeds.json", [
                'api_key' => $apiKey,
                'results' => 1
            ]);
            $status = $response->json();

            $field3 = 'field3';
            $field4 = 'field4';
            if (!empty($status['feeds'])) {
                $latestData = $status['feeds'][0];

                $relay1 = $latestData[$field3] === "1" ? "On" : "Off";
                $relay2 = $latestData[$field4] === "1" ? "On" : "Off";
            } else {
                $relay1 = null;
                $relay2 = null;
            }
        } catch (\Exception $e) {
            Log::error('Error ketika mengirim permintaan ke ThingSpeak: ' . $e->getMessage());
        }

        return view('auth.kontrolalat.kontrolalat', [
            'suhu' => $suhu,
            'suhuSebelumnya' => $suhuSebelumnya,
            'kelembaban' => $kelembaban,
            'kelembabanSebelumnya' => $kelembabanSebelumnya,
            'relay1' => $relay1,
            'relay2' => $relay2,
        ], $data);
    }

    public function grafik(Request $request)
    {
        // Menghitung tanggal satu minggu yang lalu
        $tanggalSatuMingguYangLalu = Carbon::now()->subWeek()->format('Y-m-d H:i:s');

        // Mengambil data monitor dalam rentang waktu satu minggu terakhir
        $data = Monitor::where('waktu_monitor', '>', $tanggalSatuMingguYangLalu)
            ->orderBy('waktu_monitor')
            ->get();

        $suhuData = [];
        $kelembabanData = [];
        $categories = [];

        foreach ($data as $monitor) {
            $categories[] = Carbon::parse($monitor->waktu_monitor)->format('Y-m-d H:i:s');
            $suhuData[] = $monitor->suhu_monitor;
            $kelembabanData[] = $monitor->kelembaban_monitor;
        }

        return response()->json([
            'categories' => $categories,
            'suhu' => $suhuData,
            'kelembaban' => $kelembabanData,
        ]);
    }

    public function kontrolRelay(Request $request)
    {
        try {
            $relayState = $request->input('relay');
            $apiKey = 'AZJDG542H7QZ8D5G';

            //* Mengirim data ke thingspeak
            $url = "https://api.thingspeak.com/update?api_key={$apiKey}";
            // Sesuaikan URL berdasarkan relayState
            if ($relayState == 'relay1_on') {
                $url .= "&field3=1";  // Relay 1 ON
            } elseif ($relayState == 'relay1_off') {
                $url .= "&field3=0";  // Relay 1 OFF
            } elseif ($relayState == 'relay2_on') {
                $url .= "&field4=1";  // Relay 2 ON
            } elseif ($relayState == 'relay2_off') {
                $url .= "&field4=0";  // Relay 2 OFF
            }
            // Kirim permintaan mengubah relay
            $response = Http::get($url);

            if ($response->ok()) {
                return redirect('/kontrolalat')->with('status', 'Kondisi relay telah diubah!');
            } else {
                return redirect('/kontrolalat')->with('status', 'Gagal memperbarui kondisi relay!');
            }
        } catch (\Exception $e) {
            Log::error('Error ketika mengirim permintaan ke ThingSpeak: ' . $e->getMessage());
        }
    }
}
