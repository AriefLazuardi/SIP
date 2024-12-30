<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlotWaktu extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'slot_waktu';
    protected $fillable = [
        'mulai',
        'selesai',
        'is_istirahat',
    ];

    protected $casts = [
        'is_istirahat' => 'boolean', 
        'mulai' => 'datetime',
        'selesai' => 'datetime'
    ];

    // Scope untuk memudahkan query
    public function scopeIstirahat($query)
    {
        return $query->where('is_istirahat', true);
    }

    public function scopeBukanIstirahat($query)
    {
        return $query->where('is_istirahat', false);
    }

    // Helper method
    public function isAvailableForSchedule()
    {
        return !$this->is_istirahat;
    }

    public function slotWaktuTingkatanKelas()
    {
        return $this->hasMany(SlotWaktuTingkatanKelas::class, 'slot_waktu_id');
    }
    
    public function tingkatanKelas()
    {
        return $this->belongsToMany(TingkatanKelas::class, 'slot_waktu_tingkatan_kelas', 'slot_waktu_id', 'tingkatan_kelas_id');
    }

   
}
