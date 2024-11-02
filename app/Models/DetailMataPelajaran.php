<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailMataPelajaran extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'detail_mata_pelajaran';
    protected $fillable = [
        'mata_pelajaran_id',              
        'tingkatan_kelas_id',               
        'tahun_ajaran_id', 
        'total_jam_perminggu',     
    ];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function tingkatanKelas()
    {
        return $this->belongsTo(TingkatanKelas::class, 'tingkatan_kelas_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}
