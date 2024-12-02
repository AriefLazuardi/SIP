<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
    protected $table = 'jadwal';
    protected $fillable = [
        'kelas_tugas_mengajar_id',
        'slot_waktu_mapping_id',
        'tahun_ajaran_id'
    ];

    public function kelasTugasMengajar()
    {
        return $this->belongsTo(KelasTugasMengajar::class, 'kelas_tugas_mengajar_id');
    }

    public function slotWaktuMapping()
    {
        return $this->belongsTo(SlotWaktuMapping::class, 'slot_waktu_mapping_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}
