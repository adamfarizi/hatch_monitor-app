<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harian extends Model
{
    use HasFactory;
    protected $table = 'harian';
    protected $primaryKey = 'id_harian';
    public $timestamps = true;
    protected $fillable = [
        'id_penetasan', 
        'waktu_harian', 
        'menetas', 
        'suhu_harian',
        'kelembaban_harian',
        'deskripsi',
        'bukti_harian',
    ];

    // Relationship with Penetasan
    public function penetasan()
    {
        return $this->belongsTo(Penetasan::class, 'id_penetasan', 'id_penetasan');
    }

    public function scan()
    {
        return $this->hasMany(Scan::class, 'id_harian', 'id_harian');
    }
}
