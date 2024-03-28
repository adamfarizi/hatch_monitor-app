<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
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

        $link = "http://192.168.1.10";

        return view('auth.kontrolalat.kontrolalat', [
            'suhu' => $suhu,
            'suhuSebelumnya' => $suhuSebelumnya,
            'kelembaban' => $kelembaban,
            'kelembabanSebelumnya' => $kelembabanSebelumnya,
            'link' => $link,
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

    public function getData_ThingSpeak()
    {
        $channelId = '2476613';
        $apiKey = 'OB202AUVGT70OMR2';
        $field1 = 'field1';
        $field2 = 'field2';

        $response = Http::get("https://api.thingspeak.com/channels/{$channelId}/feeds.json", [
            'api_key' => $apiKey,
            'results' => 1 // Jumlah data yang akan diambil, bisa disesuaikan
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
}
