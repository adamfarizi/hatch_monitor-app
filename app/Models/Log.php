<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;
    protected $table = 'log';
    protected $primaryKey = 'id_log';
    public $timestamps = true;
    protected $fillable = [
        'id_penetasan', 
        'waktu_log', 
        'infertil_rendah',
        'infertil_sedang',
        'infertil_tinggi',
        'fertil_rendah',
        'fertil_sedang',
        'fertil_tinggi',
        'unknown',
        'bukti_log',
    ];

    // Relationship with Harian
    public function penetasan()
    {
        return $this->belongsTo(Penetasan::class, 'id_penetasan', 'id_penetasan');
    }
}
