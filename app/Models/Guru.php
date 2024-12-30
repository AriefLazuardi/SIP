<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laratrust\Models\Role as RoleUser;

class Guru extends Model
{
    protected $table = 'guru';
    protected $fillable = [
        'name',              
        'nip',
        'tempat_lahir',
        'tanggal_lahir',
        'jabatan',
        'golongan',               
        'total_jam_perminggu', 
        'user_id',     
    ];
    use HasFactory;


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function waliKelas()
    {
        return $this->hasOne(WaliKelas::class);
    }
    public function tugasMengajar()
    {
        return $this->hasMany(TugasMengajar::class, 'guru_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsToMany(
            MataPelajaran::class,
            'tugas_mengajar',  // Nama tabel pivot
            'guru_id',         // Foreign key di tabel tugas_mengajar untuk Guru
            'mata_pelajaran_id' // Foreign key di tabel tugas_mengajar untuk MataPelajaran
        );
    }
}
