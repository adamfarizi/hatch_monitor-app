<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    use HasFactory;
    protected $table = 'scan';
    protected $primaryKey = 'id_scan';
    public $timestamps = true;
    protected $fillable = [
        'id_harian', 
        'waktu_scan', 
        'infertil_rendah',
        'infertil_sedang',
        'infertil_tinggi',
        'fertil_rendah',
        'fertil_sedang',
        'fertil_tinggi',
        'bukti_scan',
    ];

    // Relationship with Harian
    public function harian()
    {
        return $this->belongsTo(Harian::class, 'id_harian', 'id_harian');
    }

}
