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
        return $this->belongsTo(WaliKelas::class);
    }
    public function tugasMengajar()
    {
        return $this->hasMany(TugasMengajar::class, 'guru_id');
    }
}
