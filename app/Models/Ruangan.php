<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'ruangan';
    protected $fillable = [
        'nama_ruangan',          
        'kapasitas', 
    ];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

}
