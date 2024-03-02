<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infertil extends Model
{
    use HasFactory;
    protected $table = 'infertil';
    protected $primaryKey = 'id_infertil';
    public $timestamps = true;
    protected $fillable = [
        'id_harian', 
        'waktu_infertil', 
        'nomor_telur', 
        'jumlah_infertil',
        'bukti_infertil',
    ];

    // Relationship with Harian
    public function harian()
    {
        return $this->belongsTo(Harian::class, 'id_harian', 'id_harian');
    }

}
