<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Harian;
use App\Models\Penetasan;
use App\Models\Scan;
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
            ->with(['penetasan', 'scan'])
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
        if (!$img) {
            // Jika tidak ada gambar yang diunggah
            return redirect()->back()->withErrors(['error' => 'Gambar tidak ada!']);
        }
        $folderPath = "images/capture/";
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $timestamp = time();
        $dateTime = date('H-i_d-m-Y', $timestamp);
        $captureFileName = $dateTime . '.png';
        $file = $folderPath . $captureFileName;
        file_put_contents($file, $image_base64);

        //* Cek tanggal scan
        $batas_scan = Carbon::parse($penetasan->batas_scan)->startOfDay();
        $today = Carbon::now()->startOfDay();

        if ($batas_scan->lt($today)) {
            $infertilRendah = 0;
            $infertilSedang = 0;
            $infertilTinggi = 0;
            $fertilRendah = 0;
            $fertilSedang = 0;
            $fertilTinggi = 0;
            $unknown = 0;
            $scanFileName = null;

            // Move the file to the new destination folder
            // $destinationFolder = public_path('images/scan/');
            // $destinationFile = $destinationFolder . $captureFileName;
            // file_put_contents($destinationFile, $image_base64);

        } elseif ($batas_scan->eq($today)) {
            //* Mengirim gambar ke endpoint Flask
            $response = Http::attach(
                'image',
                file_get_contents(public_path($file)),
                $captureFileName
            )->post('http://localhost:8500/detect-objects');

            $responseData = $response->json();

            //* Mengambil data fertil dan infertil
            $infertilRendah = 0;
            $infertilSedang = 0;
            $infertilTinggi = 0;
            $fertilRendah = 0;
            $fertilSedang = 0;
            $fertilTinggi = 0;

            if (!empty($responseData['predictions'])) {
                $prediction = $responseData['predictions'][0];
            
                if (isset($prediction['fertil'])) {
                    $fertilRendah += $prediction['fertil']['rendah'];
                    $fertilSedang += $prediction['fertil']['sedang'];
                    $fertilTinggi += $prediction['fertil']['tinggi'];
                }
            
                if (isset($prediction['infertil'])) {
                    $infertilRendah += $prediction['infertil']['rendah'];
                    $infertilSedang += $prediction['infertil']['sedang'];
                    $infertilTinggi += $prediction['infertil']['tinggi'];
                }
            }

            //* Menghitung data unknown
            $total_telur = $penetasan->jumlah_telur;
            $total_scan = $infertilRendah + $infertilSedang + $infertilTinggi + $fertilRendah + $fertilSedang + $fertilTinggi;
            $unknown = $total_telur - $total_scan;

            //* Memindahkan gambar hasil scan YOLO ke folder public/images/scan
            $sourceFolder = base_path('yolov5/runs/detect/');
            $destinationFolder = public_path('images/scan/');
            $latestExp = $this->getLatestExpFolder($sourceFolder);

            if ($latestExp) {
                $imageFiles = glob($latestExp . '/*.jpg'); // Hanya memindahkan file gambar PNG
                foreach ($imageFiles as $image) {
                    $scanFileName = date('H-i_d-m-Y', time()) . '.png';
                    copy($image, $destinationFolder . '/' . $scanFileName);
                }
            }

        } else {
            //* Mengirim gambar ke endpoint Flask
            $response = Http::attach(
                'image',
                file_get_contents(public_path($file)),
                $captureFileName
            )->post('http://localhost:8500/detect-objects');

            $responseData = $response->json();

            //* Mengambil data fertil dan infertil
            $infertilRendah = 0;
            $infertilSedang = 0;
            $infertilTinggi = 0;
            $fertilRendah = 0;
            $fertilSedang = 0;
            $fertilTinggi = 0;

            if (!empty($responseData['predictions'])) {
                $prediction = $responseData['predictions'][0];
            
                if (isset($prediction['fertil'])) {
                    $fertilRendah += $prediction['fertil']['rendah'];
                    $fertilSedang += $prediction['fertil']['sedang'];
                    $fertilTinggi += $prediction['fertil']['tinggi'];
                }
            
                if (isset($prediction['infertil'])) {
                    $infertilRendah += $prediction['infertil']['rendah'];
                    $infertilSedang += $prediction['infertil']['sedang'];
                    $infertilTinggi += $prediction['infertil']['tinggi'];
                }
            }

            //* Menghitung data unknown
            $total_telur = $penetasan->jumlah_telur;
            $total_scan = $infertilRendah + $infertilSedang + $infertilTinggi + $fertilRendah + $fertilSedang + $fertilTinggi;
            $unknown = $total_telur - $total_scan;

            //* Memindahkan gambar hasil scan YOLO ke folder public/images/scan
            $sourceFolder = base_path('yolov5/runs/detect/');
            $destinationFolder = public_path('images/scan/');
            $latestExp = $this->getLatestExpFolder($sourceFolder);

            if ($latestExp) {
                $imageFiles = glob($latestExp . '/*.jpg');
                foreach ($imageFiles as $image) {
                    $scanFileName = date('H-i_d-m-Y', time()) . '.png';
                    copy($image, $destinationFolder . '/' . $scanFileName);
                }
            }

        }


        return view('auth.penetasan.harian.create.create', [
            'penetasan' => $penetasan,
            'suhu' => $suhu,
            'kelembaban' => $kelembaban,
            'infertilRendah' => $infertilRendah,
            'infertilSedang' => $infertilSedang,
            'infertilTinggi' => $infertilTinggi,
            'fertilRendah' => $fertilRendah,
            'fertilSedang' => $fertilSedang,
            'fertilTinggi' => $fertilTinggi,
            'unknown' => $unknown,
            'imageCapture' => $captureFileName,
            'imageScan' => $scanFileName,
        ], $data);
    }

    private function getLatestExpFolder($sourceFolder)
    {
        //* Untuk mencari folder terakhir di exp
        $folders = glob($sourceFolder . 'exp*', GLOB_ONLYDIR);
        usort($folders, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        return $folders[0] ?? null;
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
                'infertil_rendah' => 'required',
                'infertil_sedang' => 'required',
                'infertil_tinggi' => 'required',
                'fertil_rendah' => 'required',
                'fertil_sedang' => 'required',
                'fertil_tinggi' => 'required',
                'unknown' => 'required',
                'bukti_capture' => 'required',
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
                'bukti_harian' => $request->input('bukti_capture'),
            ]);

            $bukti_scan = $request->input('bukti_scan') ?? $request->input('bukti_capture');

            $scan = Scan::create([
                'id_harian' => $harian->id_harian,
                'waktu_scan' => $request->input('waktu_harian'),
                'infertil_rendah' => $request->input('infertil_rendah'),
                'infertil_sedang' => $request->input('infertil_sedang'),
                'infertil_tinggi' => $request->input('infertil_tinggi'),
                'fertil_rendah' => $request->input('fertil_rendah'),
                'fertil_sedang' => $request->input('fertil_sedang'),
                'fertil_tinggi' => $request->input('fertil_tinggi'),
                'unknown' => $request->input('unknown'),
                'bukti_scan' => $bukti_scan,
            ]);

            $rata_rata_suhu = Harian::where('id_penetasan', $id_penetasan)->avg('suhu_harian');
            $rata_rata_kelembaban = Harian::where('id_penetasan', $id_penetasan)->avg('kelembaban_harian');

            $penetasan = Penetasan::where('id_penetasan', $id_penetasan)->first();

            // Cek tanggal scan
            $batas_scan = Carbon::parse($penetasan->batas_scan)->startOfDay();
            $today = Carbon::now()->startOfDay();

            // Ambil jumlah_infertil terakhir dari model Harian
            $jumlah_infertil_terakhir = $penetasan->harian()->orderBy('created_at', 'desc')->first()->infertil_rendah +
                $penetasan->harian()->orderBy('created_at', 'desc')->first()->infertil_sedang +
                $penetasan->harian()->orderBy('created_at', 'desc')->first()->infertil_tinggi;

            //* Create prediksi menetas
            if ($batas_scan->addDay()->eq($today)) {
                // Hitung prediksi menetas
                $prediksi_menetas = $penetasan->jumlah_telur - $jumlah_infertil_terakhir;

                // Update hanya jika prediksi_menetas dihitung
                Penetasan::where('id_penetasan', $penetasan->id_penetasan)->update([
                    'prediksi_menetas' => $prediksi_menetas,
                    'total_menetas' => $penetasan->total_menetas + $request->input('menetas'),
                    'rata_rata_suhu' => $rata_rata_suhu,
                    'rata_rata_kelembaban' => $rata_rata_kelembaban,
                ]);
            } else {
                // Tetap update field lain kecuali prediksi_menetas
                Penetasan::where('id_penetasan', $penetasan->id_penetasan)->update([
                    'total_menetas' => $penetasan->total_menetas + $request->input('menetas'),
                    'rata_rata_suhu' => $rata_rata_suhu,
                    'rata_rata_kelembaban' => $rata_rata_kelembaban,
                ]);
            }

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

        $scan = Scan::where('id_harian', $id_harian)
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
            'scan' => $scan,
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
            $scan = Scan::where('id_harian', $id_harian)->first();

            $harian = Harian::where('id_harian', $id_harian)->first();
            if (!$harian) {
                throw new \Exception('Harian tidak ditemukan.');
            }

            // Hapus gambar jika ada
            if (File::exists('images/capture/' . $harian->bukti_harian)) {
                File::delete('images/capture/' . $harian->bukti_harian);
            }

            if (File::exists('images/scan/' . $scan->bukti_scan)) {
                File::delete('images/scan/' . $scan->bukti_scan);
            }

            $penetasan = Penetasan::where('id_penetasan', $id_penetasan)->first();

            // Hitung kembali prediksi menetas setelah menghapus data harian dan infertil
            // $jumlah_infertil_terakhir = 0;
            // $previous_harian = $penetasan->harian()->orderBy('created_at', 'desc')->skip(1)->first();

            // if ($previous_harian) {
            //     $jumlah_infertil_terakhir = $previous_harian->infertil_rendah +
            //         $previous_harian->infertil_sedang +
            //         $previous_harian->infertil_tinggi;
            // }

            // $prediksi_menetas = $penetasan->jumlah_telur - $jumlah_infertil_terakhir;

            // Update data penetasan setelah penghapusan
            Penetasan::where('id_penetasan', $id_penetasan)->update([
                'total_menetas' => $penetasan->total_menetas - $harian->menetas,
                // 'prediksi_menetas' => $prediksi_menetas
            ]);

            $scan->delete();
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
