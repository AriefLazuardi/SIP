<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasTugasMengajar extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'kelas_tugas_mengajar';
    protected $fillable = [
        'tugas_mengajar_id',              
        'kelas_id',
    ];

    public function tugasMengajar()
    {
        return $this->belongsTo(TugasMengajar::class, 'tugas_mengajar_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
    
}
