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
    
    public function mataPelajaran()
    {
        return $this->belongsToMany(MataPelajaran::class, 'detail_mata_pelajaran', 'tingkatan_kelas_id', 'mata_pelajaran_id');
    }
    
    public function detailMataPelajaran()
    {
    return $this->hasMany(DetailMataPelajaran::class, 'tingkatan_kelas_id');
    }
}
