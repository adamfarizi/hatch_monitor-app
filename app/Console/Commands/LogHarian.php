<?php

namespace App\Console\Commands;

use App\Models\Master;
use App\Models\Penetasan;
use App\Models\Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Support\Facades\File;

class LogHarian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:log-harian';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Capture image, send to Flask for scanning, and process the results';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Logger::info('Starting LogHarian command');

        // Capture image from Webcam 
        $imageData = $this->captureImage();

        if (!$imageData) {
            Logger::error('Failed to capture image');
            return;
        }

        // Send image to Flask and get response
        $responseData = $this->sendImageToFlask($imageData);

        if (!$responseData) {
            Logger::error('Failed to get response from Flask');
            return;
        }

        // Process the response data and save to database
        $buktiLog = $this->processResponseData($responseData);

        // Move detected images to public folder and update bukti_log
        $this->moveDetectedImages($buktiLog);

        Logger::info('LogHarian command completed');
    }

    // protected function captureImage()
    // {
    //     try {
    //         $link = Master::pluck('link1')->first();

    //         if (!$link) {
    //             Log::error('No link found in Master');
    //             return null;
    //         }

    //         $response = Http::get($link . '/capture');
    //         if ($response->ok()) {
    //             sleep(5); // Tunggu 5 detik untuk memastikan gambar tersimpan
    //             $imageUrl = $link . '/saved-photo'; // Ganti dengan URL gambar tersimpan
    //             $imageResponse = Http::get($imageUrl);
    //             return $imageResponse->body();
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('Error capturing image: ' . $e->getMessage());
    //     }

    //     return null;
    // }

    protected function captureImage()
    {
        try {
            // Path untuk gambar dummy
            $dummyImagePath = public_path('images/dummy/example2.png');

            // Membaca isi gambar dummy dari path
            $imageData = file_get_contents($dummyImagePath);

            if ($imageData) {
                return $imageData;
            } else {
                Logger::error('Failed to read dummy image data from path');
            }
        } catch (\Exception $e) {
            Logger::error('Error capturing image from webcam: ' . $e->getMessage());
        }

        return null;
    }

    protected function sendImageToFlask($imageData)
    {
        try {
            $response = Http::attach(
                'image',
                $imageData,
                'image.jpg'
            )->post('http://localhost:8500/detect-objects');

            if ($response->ok()) {
                return $response->json();
            } else {
                Logger::error('Flask server returned error: ' . $response->status());
            }
        } catch (\Exception $e) {
            Logger::error('Error sending image to Flask: ' . $e->getMessage());
        }

        return null;
    }

    protected function processResponseData($responseData)
    {
        $infertilRendah = 0;
        $infertilSedang = 0;
        $infertilTinggi = 0;
        $fertilRendah = 0;
        $fertilSedang = 0;
        $fertilTinggi = 0;

        if (!empty($responseData['predictions'])) {
            foreach ($responseData['predictions'] as $prediction) {
                if (isset($prediction['fertil'])) {
                    $fertilRendah += $prediction['fertil']['rendah'] ?? 0;
                    $fertilSedang += $prediction['fertil']['sedang'] ?? 0;
                    $fertilTinggi += $prediction['fertil']['tinggi'] ?? 0;
                }

                if (isset($prediction['infertil'])) {
                    $infertilRendah += $prediction['infertil']['rendah'] ?? 0;
                    $infertilSedang += $prediction['infertil']['sedang'] ?? 0;
                    $infertilTinggi += $prediction['infertil']['tinggi'] ?? 0;
                }
            }
        }

        // Ambil data penetasan terakhir
        $penetasan = Penetasan::latest()->first();

        // Hitung total telur dan unknown
        $total_telur = $penetasan->jumlah_telur;
        $total_scan = $infertilRendah + $infertilSedang + $infertilTinggi + $fertilRendah + $fertilSedang + $fertilTinggi;
        $unknown = $total_telur - $total_scan;

        // Simpan hasil scan ke dalam database dan ambil nama file untuk bukti_log
        $buktiLog = $this->saveToDatabase($penetasan, $infertilRendah, $infertilSedang, $infertilTinggi, $fertilRendah, $fertilSedang, $fertilTinggi, $unknown);

        return $buktiLog;
    }

    protected function saveToDatabase($penetasan, $infertilRendah, $infertilSedang, $infertilTinggi, $fertilRendah, $fertilSedang, $fertilTinggi, $unknown)
    {
        // Simpan hasil scan ke dalam database
        $log = Log::create([
            'id_penetasan' => $penetasan->id_penetasan,
            'waktu_log' => now(),
            'infertil_rendah' => $infertilRendah,
            'infertil_sedang' => $infertilSedang,
            'infertil_tinggi' => $infertilTinggi,
            'fertil_rendah' => $fertilRendah,
            'fertil_sedang' => $fertilSedang,
            'fertil_tinggi' => $fertilTinggi,
            'unknown' => $unknown,
            // Bukti_log adalah nama file gambar yang dipindahkan
            'bukti_log' => '',
        ]);

        // Ambil nama file terbaru yang dipindahkan
        $latestExp = $this->getLatestExpFolder(base_path('yolov5/runs/detect/'));
        if ($latestExp) {
            $imageFiles = glob($latestExp . '/*.jpg');
            if (!empty($imageFiles)) {
                $latestImage = $imageFiles[0]; // Ambil gambar terbaru
                $scanFileName = date('H-i_d-m-Y', time()) . '.png';
                $destinationFolder = public_path('images/log/');
                $newImagePath = $destinationFolder . '/' . $scanFileName;
                File::copy($latestImage, $newImagePath);

                // Update bukti_log dengan nama file gambar yang baru dipindahkan
                $log->update(['bukti_log' => $scanFileName]);
            }
        }

        return $scanFileName ?? null;
    }

    protected function moveDetectedImages($buktiLog)
    {
        $sourceFolder = base_path('yolov5/runs/detect/');
        $destinationFolder = public_path('images/log/');
        $latestExp = $this->getLatestExpFolder($sourceFolder);

        if ($latestExp) {
            $imageFiles = glob($latestExp . '/*.jpg');
            foreach ($imageFiles as $image) {
                $scanFileName = date('H-i_d-m-Y', time()) . '.png';
                $newImagePath = $destinationFolder . '/' . $scanFileName;
                File::copy($image, $newImagePath);
                Logger::info('Moved image to: ' . $newImagePath);

                // Perbarui entri log yang sesuai dengan buktiLog
                $log = Log::where('bukti_log', '')->latest()->first();
                if ($log) {
                    $log->update(['bukti_log' => $scanFileName]);
                } else {
                    Logger::error('No log entry found to update with bukti_log');
                }
            }
        } else {
            Logger::error('No detected images found to move');
        }
    }

    protected function getLatestExpFolder($sourceFolder)
    {
        $folders = glob($sourceFolder . 'exp*', GLOB_ONLYDIR);
        usort($folders, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        return !empty($folders) ? $folders[0] : null;
    }
}
