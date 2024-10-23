<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warna extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'warna';
    protected $fillable = [
        'kode_hex',           
    ];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }
}
