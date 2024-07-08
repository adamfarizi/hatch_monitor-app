<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master extends Model
{
    use HasFactory;

    protected $table = 'master';
    protected $primaryKey = 'id_master';
    public $timestamps = true;
    protected $fillable = [
        'link1',
        'link2',
    ];
}
