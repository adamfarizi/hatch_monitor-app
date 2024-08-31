<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Live extends Model
{
    use HasFactory;
    protected $table = 'live';
    protected $primaryKey = 'id_live';
    public $timestamps = true;
    protected $fillable = [
        'waktu',
        'class',
        'akurasi',
    ];
}
