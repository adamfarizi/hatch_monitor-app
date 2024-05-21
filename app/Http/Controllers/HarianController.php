<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Harian;
use App\Models\Infertil;
use App\Models\Penetasan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

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

    public function index_create(Request $request, $id_penetasan)
    {
        $data['title'] = 'Tambah Data Kondisi Harian';

        $penetasan = Penetasan::where('id_penetasan', $id_penetasan)
            ->first();

        //* Data Suhu
        $channelId = '2476613';
        $apiKey = 'OB202AUVGT70OMR2';
        $field1 = 'field1';
        $field2 = 'field2';

        $response = Http::get("https://api.thingspeak.com/channels/{$channelId}/feeds.json", [
            'api_key' => $apiKey,
            'results' => 1
        ]);

        $thingspeak = $response->json();
        $latestData = $thingspeak['feeds'][0];
        $suhu = $latestData[$field1];
        $kelembaban = $latestData[$field2];

        //* Menyimpan gambar infertil
        $img = $request->image;
        $folderPath = "images/scan/";
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $timestamp = time();
        $dateTime = date('H-i_d-m-Y', $timestamp);
        $fileName = $dateTime . '.png';
        $file = $folderPath . $fileName;
        file_put_contents($file, $image_base64);

        //* Cek tanggal scan
        $batas_scan = Carbon::parse($penetasan->batas_scan)->startOfDay();
        $today = Carbon::now()->startOfDay();

        if ($batas_scan->lt($today)) {
            $infertil = 0;

        } elseif ($batas_scan->eq($today)) {
            // Mengirim gambar ke endpoint Flask
            $response = Http::attach(
                'image',
                file_get_contents(public_path($file)),
                $fileName
            )->post('http://localhost:8500/detect-objects');

            $infertil = intval($response->json());

        } else {
            // Mengirim gambar ke endpoint Flask
            $response = Http::attach(
                'image',
                file_get_contents(public_path($file)),
                $fileName
            )->post('http://localhost:8500/detect-objects');

            $infertil = intval($response->json());

        }

        return view('auth.penetasan.harian.create.create', [
            'penetasan' => $penetasan,
            'suhu' => $suhu,
            'kelembaban' => $kelembaban,
            'infertil' => $infertil,
            'imagePath' => $fileName,
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
                'jumlah_infertil' => 'required',
                'bukti_scan' => 'required',
            ]);

            // Tentukan nilai suhu berdasarkan pilihan radio
            $suhu = $request->input('suhu_radio') == 'scan' ? $request->input('suhu_scan') : $request->input('suhu_manual');

            // Tentukan nilai kelembaban berdasarkan pilihan radio
            $kelembaban = $request->input('kelembaban_radio') == 'scan' ? $request->input('kelembaban_scan') : $request->input('kelembaban_manual');

            $deskripsi = $request->input('deskripsi') == null ? 'Tidak ada catatan' : $request->input('deskripsi');

            $harian = Harian::create([
                'id_penetasan' => $id_penetasan,
                'waktu_harian' => $request->input('waktu_harian'),
                'menetas' => $request->input('menetas'),
                'suhu_harian' => $suhu,
                'kelembaban_harian' => $kelembaban,
                'deskripsi' => $deskripsi,
                'bukti_harian' => $request->input('bukti_scan'),
            ]);

            $infertil = Infertil::create([
                'id_harian' => $harian->id_harian,
                'waktu_infertil' => $request->input('waktu_harian'),
                'nomor_telur' => null,
                'jumlah_infertil' => $request->input('jumlah_infertil'),
                'bukti_infertil' => $request->input('bukti_scan'),
            ]);

            $rata_rata_suhu = Harian::where('id_penetasan', $id_penetasan)->avg('suhu_harian');
            $rata_rata_kelembaban = Harian::where('id_penetasan', $id_penetasan)->avg('kelembaban_harian');

            $penetasan = Penetasan::where('id_penetasan', $id_penetasan)->first();

            // Cek tanggal scan
            $batas_scan = Carbon::parse($penetasan->batas_scan)->startOfDay();
            $today = Carbon::now()->startOfDay();

            if ($batas_scan->lt($today)) {
                // $prediksi_menetas = $penetasan->prediksi_menetas;

            } elseif ($batas_scan->eq($today)) {
                // $prediksi_menetas = $penetasan->jumlah_telur - $request->input('jumlah_infertil');

            } else {
                // $prediksi_menetas = $penetasan->jumlah_telur - $request->input('jumlah_infertil');

            }
            Penetasan::where('id_penetasan', $id_penetasan)->update([
                // 'prediksi_menetas' => $prediksi_menetas,
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

        $infertil = Infertil::where('id_harian', $id_harian)
            ->first();

        // Data Suhu
        $channelId = '2476613';
        $apiKey = 'OB202AUVGT70OMR2';
        $field1 = 'field1';
        $field2 = 'field2';

        $response = Http::get("https://api.thingspeak.com/channels/{$channelId}/feeds.json", [
            'api_key' => $apiKey,
            'results' => 1
        ]);

        $thingspeak = $response->json();
        $latestData = $thingspeak['feeds'][0];
        $suhu = $latestData[$field1];
        $kelembaban = $latestData[$field2];

        return view('auth.penetasan.harian.edit.edit', [
            'penetasan' => $penetasan,
            'harian' => $harian,
            'infertil' => $infertil,
            'suhu' => $suhu,
            'kelembaban' => $kelembaban,
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
            $infertil = Infertil::where('id_harian', $id_harian)->first();

            $harian = Harian::where('id_harian', $id_harian)->first();
            if (!$harian) {
                throw new \Exception('Harian tidak ditemukan.');
            }

            // Hapus gambar jika ada
            if (File::exists('images/scan/' . $harian->bukti_harian)) {
                File::delete('images/scan/' . $harian->bukti_harian);
            }

            $penetasan = Penetasan::where('id_penetasan', $id_penetasan)->first();
            Penetasan::where('id_penetasan', $id_penetasan)->update([
                'total_menetas' => $penetasan->total_menetas - $harian->menetas,
                // 'prediksi_menetas' => $penetasan->prediksi_menetas + $infertil->jumlah_infertil
            ]);

            $infertil->delete();
            $harian->delete();

            $rata_rata_suhu = Harian::where('id_penetasan', $id_penetasan)->avg('suhu_harian');
            $rata_rata_kelembaban = Harian::where('id_penetasan', $id_penetasan)->avg('kelembaban_harian');

            // Menggunakan operator ternary untuk menggantikan null dengan 0
            $rata_rata_suhu = $rata_rata_suhu ?? 0;
            $rata_rata_kelembaban = $rata_rata_kelembaban ?? 0;

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
