<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiBelajar extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'sesi_belajar';
    protected $fillable = [
        'nama',   
    ];

    public function slotWaktuTingkatanKelas()
    {
        return $this->hasMany(SlotWaktuTingkatanKelas::class, 'sesi_belajar_id');
    }
}
