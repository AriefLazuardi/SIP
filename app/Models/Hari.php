<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hari extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'hari';
    protected $fillable = [
        'nama_hari',       
    ];

    public function slotWaktu()
    {
        return $this->hasMany(SlotWaktu::class, 'hari_id');
    }
}
