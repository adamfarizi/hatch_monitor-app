<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    use HasFactory;
    protected $table = 'monitor';
    protected $primaryKey = 'id_monitor';
    public $timestamps = true;
    protected $fillable = [
        'id_penetasan', 
        'waktu_monitor', 
        'suhu_monitor', 
        'kelembaban_monitor',
    ];

    // Relationship with Penetasan
    public function penetasan()
    {
        return $this->belongsTo(Penetasan::class, 'id_penetasan', 'id_penyetasan');
    }
}
