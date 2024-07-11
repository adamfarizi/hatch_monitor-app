<?php

namespace App\Http\Controllers;

use App\Models\Harian;
use App\Models\Penetasan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Log;
use Yajra\DataTables\Facades\DataTables;

class LogController extends Controller
{
    public function index(Request $request, $id_penetasan)
    {
        $data['title'] = 'Log Harian Penetasan';

        $penetasan = Penetasan::where('id_penetasan', $id_penetasan)
            ->first();

        if ($request->ajax()) {
            $filterBulan = $request->filterBulan;
            $data = Log::where('id_penetasan', $id_penetasan)
                ->orderByDesc('waktu_log')
                ->with(['penetasan'])
                ->get();
            return DataTables::of($data)
                ->make(true);
        }

        $logs = Log::all();

        return view('auth.penetasan.harian.log.log', [
            'penetasan' => $penetasan,
            'logs' => $logs,
        ], $data);
    }
}
