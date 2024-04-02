<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use App\Models\Penetasan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index()
    {
        $data['title'] = 'Beranda';

        $suhu = Monitor::orderBy('waktu_monitor', 'desc')->pluck('suhu_monitor')->first();
        $kelembaban = Monitor::orderBy('waktu_monitor', 'desc')->pluck('kelembaban_monitor')->first();

        $suhuSebelumnya = Monitor::orderBy('waktu_monitor', 'desc')->skip(1)->take(1)->pluck('suhu_monitor')->first();
        $kelembabanSebelumnya = Monitor::orderBy('waktu_monitor', 'desc')->skip(1)->take(1)->pluck('kelembaban_monitor')->first();

        $telur = Penetasan::sum('total_menetas');
        $penetasan = Penetasan::count();

        return view('auth.beranda.beranda', [
            'suhu' => $suhu,
            'suhuSebelumnya' => $suhuSebelumnya,
            'kelembaban' => $kelembaban,
            'kelembabanSebelumnya' => $kelembabanSebelumnya,
            'telur' => $telur,
            'penetasan' => $penetasan,
        ], $data);
    }

}
