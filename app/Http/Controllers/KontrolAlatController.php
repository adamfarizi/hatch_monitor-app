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

        //* Mengambil data relay
        try {
            $channelId = '2564192';
            $apiKey = 'UA1FQEG08D4IHX3K';
        
            // URL untuk field1
            $urlfield1 = "https://api.thingspeak.com/channels/{$channelId}/fields/1.json?api_key={$apiKey}&results=1";
            // URL untuk field2
            $urlfield2 = "https://api.thingspeak.com/channels/{$channelId}/fields/2.json?api_key={$apiKey}&results=1";
        
            // Mengambil data untuk field1
            $responsefield1 = Http::get($urlfield1);
            $statusfield1 = $responsefield1->json();
        
            // Mengambil data untuk field2
            $responsefield2 = Http::get($urlfield2);
            $statusfield2 = $responsefield2->json();
        
            // Memeriksa dan mengambil nilai terbaru untuk field1
            if (isset($statusfield1['feeds']) && !empty($statusfield1['feeds'])) {
                $latestDatafield1 = $statusfield1['feeds'][0];
                if (isset($latestDatafield1['field1'])) {
                    $relay1 = $latestDatafield1['field1'] === "1" ? "On" : "Off";
                } else {
                    $relay1 = null;
                }
            } else {
                $relay1 = null;
            }
        
            // Memeriksa dan mengambil nilai terbaru untuk field2
            if (isset($statusfield2['feeds']) && !empty($statusfield2['feeds'])) {
                $latestDatafield2 = $statusfield2['feeds'][0];
                if (isset($latestDatafield2['field2'])) {
                    $relay2 = $latestDatafield2['field2'] === "1" ? "On" : "Off";
                } else {
                    $relay2 = null;
                }
            } else {
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
            $channelId = '2564192';
            $readApiKey = 'UA1FQEG08D4IHX3K';
            $writeApiKey = 'GID60J89SWH0OCCL';
        
            // URL untuk mengambil data terbaru
            $latestDataUrl = "https://api.thingspeak.com/channels/{$channelId}/feeds.json?api_key={$readApiKey}&results=1";
        
            // Mengambil data terbaru dari ThingSpeak
            $latestDataResponse = Http::get($latestDataUrl);
            $latestData = $latestDataResponse->json()['feeds'][0]; // Ambil data terbaru
        
            // Inisialisasi data untuk dikirim
            $data = [];
        
            // Sesuaikan data berdasarkan relayState
            if ($relayState == 'relay1_on' || $relayState == 'relay1_off') {
                $data['field1'] = $relayState == 'relay1_on' ? 1 : 0;  // Relay 1 ON atau OFF
                // Ambil nilai field2 dari data terbaru
                $data['field2'] = isset($latestData['field2']) ? $latestData['field2'] : 0;
            } elseif ($relayState == 'relay2_on' || $relayState == 'relay2_off') {
                $data['field2'] = $relayState == 'relay2_on' ? 1 : 0;  // Relay 2 ON atau OFF
                // Ambil nilai field1 dari data terbaru
                $data['field1'] = isset($latestData['field1']) ? $latestData['field1'] : 0;
            }
        
            // Kirim permintaan untuk memperbarui field
            $writeUrl = "https://api.thingspeak.com/update?api_key={$writeApiKey}";
            $writeResponse = Http::post($writeUrl, $data);
            
            if ($writeResponse->ok()) {
                if ($writeResponse->body() == 0) {
                    return redirect('/kontrolalat')->with('loading', 'Sistem sedang memuat, ulangi proses!');
                } else {
                    return redirect('/kontrolalat')->with('status', 'Kondisi relay telah diubah!');
                }
            } 
            else {
                return redirect('/kontrolalat')->with('error', 'Gagal memperbarui kondisi relay!');
            }
        } catch (\Exception $e) {
            Log::error('Error ketika mengirim permintaan ke ThingSpeak: ' . $e->getMessage());
        }              
    }
}
