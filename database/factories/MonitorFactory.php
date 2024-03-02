<?php

namespace Database\Factories;

use App\Models\Monitor;
use App\Models\Penetasan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Monitor>
 */
class MonitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Monitor::class;

    public function definition(): array
    {   
        // Mendapatkan tanggal awal bulan ini
        $startDate = Carbon::now()->startOfYear();

        // Menambahkan jam sesuai dengan index (setiap 5 jam)
        $dateTime = $startDate->copy()->addHours($this->faker->unique()->numberBetween(0, now()->diffInHours($startDate)));

        return [
            'waktu_monitor' => $dateTime,
            'suhu_monitor' => $this->faker->randomFloat(2, 38, 40),
            'kelembaban_monitor' => $this->faker->randomFloat(2, 48, 55),
        ];
    }
}
