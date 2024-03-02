<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penetasan extends Model
{
    use HasFactory;
    protected $table = 'penetasan';
    protected $primaryKey = 'id_penetasan';
    public $timestamps = true;
    protected $fillable = [
        'id_peternak', 
        'tanggal_mulai', 
        'tanggal_selesai', 
        'jumlah_telur', 
        'prediksi_menetas',
        'total_menetas',
        'rata_rata_suhu',
        'rata_rata_kelembaban',
    ];

    // Relationship with Peternak
    public function peternak()
    {
        return $this->belongsTo(User::class, 'id_peternak', 'id_peternak');
    }

    // Relationship with Harian
    public function harian()
    {
        return $this->hasMany(Harian::class, 'id_penetasan', 'id_penetasan');
    }

    // Relationship with Monitor
    public function monitor()
    {
        return $this->hasMany(Monitor::class, 'id_penetasan', 'id_penetasan');
    }
}
