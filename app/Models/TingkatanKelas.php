<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TingkatanKelas extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'tingkatan_kelas';
    protected $fillable = [
        'nama_tingkatan',           
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'tingkatan_kelas_id');
    }

    public function slotWaktu()
    {
        return $this->hasMany(SlotWaktu::class, 'tingkatan_kelas_id');
    }

}
