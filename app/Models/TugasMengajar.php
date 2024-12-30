<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasMengajar extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tugas_mengajar';
    protected $fillable = [
        'guru_id',     
        'mata_pelajaran_id'            
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function kelas()
    {
    return $this->belongsToMany(Kelas::class, 'kelas_tugas_mengajar', 'tugas_mengajar_id', 'kelas_id')
                ->with('tingkatanKelas'); // Eager load tingkatanKelas
    }

    public function kelasTugasMengajar()
    {
        return $this->hasMany(KelasTugasMengajar::class, 'tugas_mengajar_id');
    }

}
