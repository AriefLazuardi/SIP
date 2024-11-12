<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlotWaktuMapping extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'slot_waktu_mapping';
    protected $fillable = [
        'slot_waktu_tingkatan_kelas_id',
        'hari_id',
    ];

    public function slotWaktuTingkatanKelas()
    {
        return $this->belongsTo(SlotWaktuTingkatanKelas::class, 'slot_waktu_tingkatan_kelas_id');
    }
    
    public function sesiBelajar()
    {
        return $this->belongsTo(SesiBelajar::class, 'slot_waktu_tingkatan_kelas_id');
    }

    public function hari()
    {
        return $this->belongsTo(Hari::class, 'hari_id');
    }
}
