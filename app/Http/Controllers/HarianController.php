<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Harian;
use App\Models\Penetasan;
use Illuminate\Http\Request;

class HarianController extends Controller
{
    public function index(Request $request, $id_penetasan)
    {
        $data['title'] = 'Cek Kondisi Harian';

        $harians = Harian::where('id_penetasan', $id_penetasan)
            ->orderByDesc('waktu_harian')
            ->with(['penetasan', 'infertil'])
            ->get();

        $penetasan = Penetasan::where('id_penetasan', $id_penetasan)
            ->first();

        return view('auth.penetasan.harian.harian', [
            'harians' => $harians,
            'penetasan' => $penetasan,
        ], $data);
    }

    public function index_create($id_penetasan)
    {
        $data['title'] = 'Tambah Data Kondisi Harian';

        $penetasan = Penetasan::where('id_penetasan', $id_penetasan)
            ->first();

        return view('auth.penetasan.harian.create.create', [
            'penetasan' => $penetasan,
        ], $data);
    }

    public function create(Request $request, $id_penetasan)
    {
        try {

            $request->validate([
                'waktu_harian' => 'required|date',
                'menetas' => 'required',
                'suhu_radio' => 'required',
                'suhu_scan' => 'required_if:suhu_radio,scan',
                'suhu_manual' => 'required_if:suhu_radio,manual',
                'kelembaban_radio' => 'required',
                'kelembaban_scan' => 'required_if:kelembaban_radio,scan',
                'kelembaban_manual' => 'required_if:kelembaban_radio,manual',
            ]);

            // Tentukan nilai suhu berdasarkan pilihan radio
            $suhu = $request->input('suhu_radio') == 'scan' ? $request->input('suhu_scan') : $request->input('suhu_manual');

            // Tentukan nilai kelembaban berdasarkan pilihan radio
            $kelembaban = $request->input('kelembaban_radio') == 'scan' ? $request->input('kelembaban_scan') : $request->input('kelembaban_manual');

            $deskripsi = $request->input('deskripsi') == null ? 'Tidak ada catatan' : $request->input('deskripsi');

            Harian::create([
                'id_penetasan' => $id_penetasan,
                'waktu_harian' => $request->input('waktu_harian'),
                'menetas' => $request->input('menetas'),
                'suhu_harian' => $suhu,
                'kelembaban_harian' => $kelembaban,
                'deskripsi' => $deskripsi,
            ]);

            $rata_rata_suhu = Harian::where('id_penetasan', $id_penetasan)->avg('suhu_harian');
            $rata_rata_kelembaban = Harian::where('id_penetasan', $id_penetasan)->avg('kelembaban_harian');

            $penetasan = Penetasan::where('id_penetasan', $id_penetasan)->first();
            Penetasan::where('id_penetasan', $id_penetasan)->update([
                'total_menetas' => $penetasan->total_menetas + $request->input('menetas'),
                'rata_rata_suhu' => $rata_rata_suhu,
                'rata_rata_kelembaban' => $rata_rata_kelembaban,
            ]);

            $url = url('/penetasan/' . $id_penetasan . '/harian');
            return redirect()->away($url)->with('success', 'Data harian baru berhasil ditambahkan !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function index_edit(Request $request, $id_penetasan, $id_harian)
    {
        $data['title'] = 'Edit Data Kondisi Harian';

        $penetasan = Penetasan::where('id_penetasan', $id_penetasan)
            ->first();

        $harian = Harian::where('id_harian', $id_harian)
            ->first();

        return view('auth.penetasan.harian.edit.edit', [
            'penetasan' => $penetasan,
            'harian' => $harian,
        ], $data);
    }

    public function edit(Request $request, $id_penetasan, $id_harian)
    {
        try {
            $request->validate([
                'waktu_harian' => 'required|date',
                'menetas' => 'required',
                'suhu_radio' => 'required',
                'suhu_scan' => 'required_if:suhu_radio,scan',
                'suhu_manual' => 'required_if:suhu_radio,manual',
                'kelembaban_radio' => 'required',
                'kelembaban_scan' => 'required_if:kelembaban_radio,scan',
                'kelembaban_manual' => 'required_if:kelembaban_radio,manual',
            ]);

            // Tentukan nilai suhu berdasarkan pilihan radio
            $suhu = $request->input('suhu_radio') == 'scan' ? $request->input('suhu_scan') : $request->input('suhu_manual');

            // Tentukan nilai kelembaban berdasarkan pilihan radio
            $kelembaban = $request->input('kelembaban_radio') == 'scan' ? $request->input('kelembaban_scan') : $request->input('kelembaban_manual');

            $deskripsi = $request->input('deskripsi') == null ? 'Tidak ada catatan' : $request->input('deskripsi');

            Harian::where('id_harian', $id_harian)->update([
                'suhu_harian' => $suhu,
                'kelembaban_harian' => $kelembaban,
            ]);

            $rata_rata_suhu = Harian::where('id_penetasan', $id_penetasan)->avg('suhu_harian');
            $rata_rata_kelembaban = Harian::where('id_penetasan', $id_penetasan)->avg('kelembaban_harian');

            $harian = Harian::where('id_harian', $id_harian)->first();
            $penetasan = Penetasan::where('id_penetasan', $id_penetasan)->first();

            $new_total_menetas = $penetasan->total_menetas;
            if ($request->input('menetas') == $harian->menetas) {
                Penetasan::where('id_penetasan', $id_penetasan)->update([
                    'rata_rata_suhu' => $rata_rata_suhu,
                    'rata_rata_kelembaban' => $rata_rata_kelembaban,
                ]);
            } elseif ($request->input('menetas') < $harian->menetas) {
                $old_menetas = $harian->menetas;
                $new_menetas = $request->input('menetas');
                if ($new_menetas == 0) {
                    $new_total_menetas -= $old_menetas;
                    Penetasan::where('id_penetasan', $id_penetasan)->update([
                        'total_menetas' => $new_total_menetas,
                        'rata_rata_suhu' => $rata_rata_suhu,
                        'rata_rata_kelembaban' => $rata_rata_kelembaban,
                    ]);
                } else {
                    $menetas_difference = $old_menetas - $new_menetas;
                    $new_total_menetas = $penetasan->total_menetas - $menetas_difference;
                    Penetasan::where('id_penetasan', $id_penetasan)->update([
                        'total_menetas' => $new_total_menetas,
                        'rata_rata_suhu' => $rata_rata_suhu,
                        'rata_rata_kelembaban' => $rata_rata_kelembaban,
                    ]);
                }
            } else {
                $new_total_menetas += ($request->input('menetas') - $harian->menetas);
                Penetasan::where('id_penetasan', $id_penetasan)->update([
                    'total_menetas' => $new_total_menetas,
                    'rata_rata_suhu' => $rata_rata_suhu,
                    'rata_rata_kelembaban' => $rata_rata_kelembaban,
                ]);
            }

            Harian::where('id_harian', $id_harian)->update([
                'waktu_harian' => $request->input('waktu_harian'),
                'menetas' => $request->input('menetas'),
                'deskripsi' => $deskripsi,
            ]);

            $url = url('/penetasan/' . $id_penetasan . '/harian');
            return redirect()->away($url)->with('success', 'Data harian berhasil diubah !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function delete($id_penetasan, $id_harian)
    {
        try {
            $harian = Harian::where('id_harian', $id_harian)->first();
            if (!$harian) {
                throw new \Exception('Harian tidak ditemukan.');
            }

            $penetasan = Penetasan::where('id_penetasan', $id_penetasan)->first();
            Penetasan::where('id_penetasan', $id_penetasan)->update([
                'total_menetas' => $penetasan->total_menetas - $harian->menetas,
            ]);

            $harian->delete();

            $rata_rata_suhu = Harian::where('id_penetasan', $id_penetasan)->avg('suhu_harian');
            $rata_rata_kelembaban = Harian::where('id_penetasan', $id_penetasan)->avg('kelembaban_harian');
            Penetasan::where('id_penetasan', $id_penetasan)->update([
                'rata_rata_suhu' => $rata_rata_suhu,
                'rata_rata_kelembaban' => $rata_rata_kelembaban,
            ]);

            return redirect()->back()->with('success', 'Data harian berhasil dihapus !');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }
}
