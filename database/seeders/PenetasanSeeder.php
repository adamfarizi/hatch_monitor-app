<?php

namespace Database\Seeders;

use App\Models\Harian;
use App\Models\Penetasan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PenetasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Buat data untuk tabel Penetasan
        $penetasan = Penetasan::create([
            'id_peternak' => 1,
            'tanggal_mulai' => now()->subDays(22),
            'tanggal_selesai' => now(), // Tanggal selesai adalah 23 hari setelah tanggal mulai
            'jumlah_telur' => 100,
            'prediksi_menetas' => 80,
            'total_menetas' => 0,
            'rata_rata_suhu' => 39,
            'rata_rata_kelembaban' => 50.2,
        ]);

        // Buat data untuk tabel Harian
        $start_date = $penetasan->tanggal_mulai;
        $end_date = now();

        // Perulangan dari tanggal mulai hingga tanggal selesai
        while ($start_date <= $end_date) {
            // Buat entri untuk masing-masing waktu harian
            $times = ['06:00:00', '12:00:00', '16:00:00', '21:00:00'];
            foreach ($times as $time) {
                Harian::create([
                    'id_penetasan' => $penetasan->id_penetasan,
                    'waktu_harian' => Carbon::parse($start_date)->format('Y-m-d') . ' ' . $time,
                    'menetas' => rand(0, 1),
                    'suhu_harian' => rand(38, 40) + (mt_rand() / mt_getrandmax()),
                    'kelembaban_harian' => rand(500, 530) / 10.0,
                    'deskripsi' => 'Deskripsi harian pada tanggal ' . Carbon::parse($start_date)->format('Y-m-d') . ' jam ' . $time,
                    'bukti_harian' => null, // Ganti dengan URL bukti jika ada
                ]);
            }
            // Tambahkan 1 hari
            $start_date->addDay();
        }

        // Perbarui total_menetas, rata_rata_suhu, dan rata_rata_kelembaban pada Penetasan
        $total_menetas = Harian::where('id_penetasan', $penetasan->id_penetasan)->sum('menetas');
        $rata_rata_suhu = Harian::where('id_penetasan', $penetasan->id_penetasan)->avg('suhu_harian');
        $rata_rata_kelembaban = Harian::where('id_penetasan', $penetasan->id_penetasan)->avg('kelembaban_harian');

        $penetasan->update([
            'total_menetas' => $total_menetas,
            'rata_rata_suhu' => $rata_rata_suhu,
            'rata_rata_kelembaban' => $rata_rata_kelembaban,
        ]);
    }

}
