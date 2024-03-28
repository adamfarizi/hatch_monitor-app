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

        $suhu = Monitor::pluck('suhu_monitor')->first();
        $kelembaban = Monitor::pluck('suhu_monitor')->first();
        $telur = Penetasan::sum('total_menetas');
        $penetasan = Penetasan::count();

        return view('auth.beranda.beranda', [
            'suhu' => $suhu,
            'kelembaban' => $kelembaban,
            'telur' => $telur,
            'penetasan' => $penetasan,
        ], $data);
    }

}
