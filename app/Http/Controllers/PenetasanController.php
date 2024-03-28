<?php

namespace App\Http\Controllers;

use App\Models\Penetasan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class PenetasanController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Penetasan';

        if ($request->ajax()) {
            $filterBulan = $request->filterBulan;

            $data = Penetasan::where('tanggal_mulai', 'like', $filterBulan . '%')
                ->orderByDesc('tanggal_mulai')
                ->get();
            return DataTables::of($data)
                ->make(true);
        }

        $penetasans = Penetasan::with('harian')->get();

        return view('auth.penetasan.penetasan', [
            'penetasans' => $penetasans,
        ], $data);
    }

    public function grafik(Request $request)
    {
        // Menghitung tanggal satu minggu yang lalu
        $tanggalSatuMingguYangLalu = Carbon::now()->subMonths(3)->format('Y-m-d H:i:s');

        // Mengambil data monitor dalam rentang waktu satu minggu terakhir
        $data = Penetasan::where('tanggal_mulai', '>', $tanggalSatuMingguYangLalu)
            ->orderBy('tanggal_mulai')
            ->get();

        $jumlahTelur = [];
        $menetas = [];
        $categories = [];

        foreach ($data as $monitor) {
            $categories[] = Carbon::parse($monitor->tanggal_mulai)->format('m-d');
            $jumlahTelur[] = $monitor->jumlah_telur;
            $menetas[] = $monitor->total_menetas;
        }

        return response()->json([
            'categories' => $categories,
            'jumlahTelur' => $jumlahTelur,
            'menetas' => $menetas,
        ]);
    }

    public function create(Request $request)
    {
        try {
            $this->validate($request,[
                'tanggal_mulai' => 'required',
                'jumlah_telur' => 'required',
            ],[
                'tanggal_mulai' => 'Masukkan tanggal mulai terlebih dahulu !',
                'jumlah_telur' => 'Masukan jumlah telur terlebih dahulu !',
            ]);

            $tanggal_mulai = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('tanggal_mulai'));
            $tanggal_selesai = $tanggal_mulai->addDays(23);
            $jumlah_telur = $request->input('jumlah_telur');

            $penetasan = Penetasan::create([
                'tanggal_mulai' => $request->input('tanggal_mulai'),
                'tanggal_selesai' => $tanggal_selesai,
                'jumlah_telur' => $jumlah_telur,
                'id_peternak' => Auth::user()->id_peternak,
            ]);

            return redirect()->back()->with('success', 'Penetasan baru berhasil ditambahkan !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function edit(Request $request, $id_penetasan)
    {
        try {
            $this->validate($request,[
                'tanggal_mulai' => 'required',
                'jumlah_telur' => 'required',
            ],[
                'tanggal_mulai' => 'Masukkan tanggal mulai terlebih dahulu !',
                'jumlah_telur' => 'Masukan jumlah telur terlebih dahulu !',
            ]);

            $tanggal_mulai = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('tanggal_mulai'));
            $tanggal_selesai = $tanggal_mulai->addDays(23);
            $jumlah_telur = $request->input('jumlah_telur');

            $penetasan = Penetasan::where('id_penetasan', $id_penetasan)->update([
                'tanggal_mulai' => $request->input('tanggal_mulai'),
                'tanggal_selesai' => $tanggal_selesai,
                'jumlah_telur' => $jumlah_telur,
            ]);

            return redirect()->back()->with('success', 'Penetasan berhasil diubah !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function delete($id_penetasan)
    {
        try {
            $penetasan = Penetasan::where('id_penetasan', $id_penetasan);
            if (!$penetasan) {
                throw new \Exception('Penetasan tidak ditemukan.');
            }
            $penetasan->delete();

            return redirect()->back()->with('success', 'Penetasan berhasil dihapus !');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }
}
