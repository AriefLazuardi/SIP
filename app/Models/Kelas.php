<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'kelas';
    protected $fillable = [
        'tingkatan_kelas_id',          
        'nama_kelas',
        'total_slot_perminggu', 
    ];

    public function tingkatanKelas()
    {
        return $this->belongsTo(TingkatanKelas::class, 'tingkatan_kelas_id');
    }

    public function tugasMengajar()
    {
        return $this->belongsToMany(TugasMengajar::class, 'kelas_tugas_mengajar', 'kelas_id', 'tugas_mengajar_id');
    }
    
}
