<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
    protected $table = 'jadwal';
    protected $fillable = [
        'tugas_mengajar_id',
        'slot_waktu_mapping_id',
        'ruangan_id',
        'tahun_ajaran_id'
    ];

    public function tugasMengajar()
    {
        return $this->belongsTo(TugasMengajar::class);
    }

    public function slotWaktuMapping()
    {
        return $this->belongsTo(SlotWaktuMapping::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
