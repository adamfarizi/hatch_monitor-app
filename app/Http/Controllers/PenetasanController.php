<?php

namespace App\Http\Controllers;

use App\Models\Harian;
use App\Models\Infertil;
use App\Models\Penetasan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
        $BatasTanggal = Carbon::now()->subMonths(3)->format('Y-m-d H:i:s');

        // Mengambil data monitor dalam rentang waktu satu minggu terakhir
        $data = Penetasan::where('tanggal_mulai', '>', $BatasTanggal)
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
            $this->validate($request, [
                'tanggal_mulai' => 'required',
                'jumlah_telur' => 'required',
            ], [
                'tanggal_mulai' => 'Masukkan tanggal mulai terlebih dahulu !',
                'jumlah_telur' => 'Masukan jumlah telur terlebih dahulu !',
            ]);

            $tanggal_mulai = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('tanggal_mulai'));
            $tanggal_selesai = $tanggal_mulai->copy()->addDays(23);
            $batas_scan = $tanggal_mulai->copy()->addDays(10);
            $jumlah_telur = $request->input('jumlah_telur');

            $penetasan = Penetasan::create([
                'tanggal_mulai' => $request->input('tanggal_mulai'),
                'tanggal_selesai' => $tanggal_selesai,
                'batas_scan' => $batas_scan,
                'prediksi_menetas' => $jumlah_telur,
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
            $this->validate($request, [
                'tanggal_mulai' => 'required',
                'jumlah_telur' => 'required',
            ], [
                'tanggal_mulai' => 'Masukkan tanggal mulai terlebih dahulu !',
                'jumlah_telur' => 'Masukan jumlah telur terlebih dahulu !',
            ]);

            $tanggal_mulai = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('tanggal_mulai'));
            $tanggal_selesai = $tanggal_mulai->copy()->addDays(23);
            $batas_scan = $tanggal_mulai->copy()->addDays(10);
            $jumlah_telur = $request->input('jumlah_telur');

            $dataHarianExists = Harian::where('id_penetasan', $id_penetasan)->exists();

            if ($dataHarianExists) {
                $harian = Harian::where('id_penetasan', $id_penetasan)->get();
                $id_harian = $harian->pluck('id_harian');
                $jumlah_infertil = Infertil::whereIn('id_harian', $id_harian)
                    ->sum('jumlah_infertil');

                // $prediksi_menetas = $jumlah_telur - $jumlah_infertil;
            } else {
                // $prediksi_menetas = $jumlah_telur;
            }

            Penetasan::where('id_penetasan', $id_penetasan)->update([
                'tanggal_mulai' => $request->input('tanggal_mulai'),
                'tanggal_selesai' => $tanggal_selesai,
                'batas_scan' => $batas_scan,
                // 'prediksi_menetas' => $prediksi_menetas,
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
            $id_harian_penetasan = Harian::where('id_penetasan', $id_penetasan)->pluck('id_harian');
            $infertilList = Infertil::whereIn('id_harian', $id_harian_penetasan)->get();
            $harianList = Harian::where('id_penetasan', $id_penetasan)->get();

            // Hapus gambar jika ada dan hapus data Harian
            foreach ($infertilList as $infertil) {
                if (File::exists(public_path('images/scan/' . $infertil->bukti_infertil))) {
                    File::delete(public_path('images/scan/' . $infertil->bukti_infertil));
                }
                $infertil->delete();
            }

            foreach ($harianList as $harian) {
                if (File::exists(public_path('images/capture/' . $harian->bukti_harian))) {
                    File::delete(public_path('images/capture/' . $harian->bukti_harian));
                }
                $harian->delete();
            }

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
