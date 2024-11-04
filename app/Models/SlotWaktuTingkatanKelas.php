<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlotWaktuTingkatanKelas extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'slot_waktu_tingkatan_kelas';
    protected $fillable = [
        'slot_waktu_id',
        'tingkatan_kelas_id',
        'sesi_belajar_id',
    ];

    public function slotWaktu()
    {
        return $this->belongsTo(SlotWaktu::class, 'slot_waktu_id');
    }    

    public function tingkatanKelas()
    {
        return $this->belongsTo(TingkatanKelas::class, 'tingkatan_kelas_id');
    }

    public function sesiBelajar()
    {
        return $this->belongsTo(SesiBelajar::class, 'sesi_belajar_id');
    }

    public function slotWaktuMapping()
    {
        return $this->hasMany(SlotWaktuMapping::class, 'slot_waktu_tingkatan_kelas_id');
    }
}
