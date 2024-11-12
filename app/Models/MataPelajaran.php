<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'mata_pelajaran';
    protected $fillable = [
        'nama',
        'warna_id'                
    ];

    public function warna()
    {
        return $this->belongsTo(Warna::class);
    }

    public function detailMataPelajaran()
    {
        return $this->hasMany(DetailMataPelajaran::class, 'mata_pelajaran_id');
    }
    
    public function tingkatanKelas()
    {
        return $this->belongsToMany(TingkatanKelas::class, 'detail_mata_pelajaran', 'mata_pelajaran_id', 'tingkatan_kelas_id');
    }

    public function hasDetail()
    {
        return !is_null($this->detailMataPelajaran);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($mataPelajaran) {
            $mataPelajaran->detailMataPelajaran()->delete();
        });
    }
}
