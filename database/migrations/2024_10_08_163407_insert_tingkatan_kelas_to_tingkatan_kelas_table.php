<?php

use App\Models\TingkatanKelas;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tingkatanSatu = TingkatanKelas::create([
            'nama_tingkatan' => 1,
        ]);
        $tingkatanDua = TingkatanKelas::create([
            'nama_tingkatan' => 2,
        ]);
        $tingkatanTiga = TingkatanKelas::create([
            'nama_tingkatan' => 3,
        ]);
        $tingkatanEmpat = TingkatanKelas::create([
            'nama_tingkatan' => 4,
        ]);
        $tingkatanLima = TingkatanKelas::create([
            'nama_tingkatan' => 5,
        ]);
        $tingkatanEnam = TingkatanKelas::create([
            'nama_tingkatan' => 6,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tingkatan_kelas', function (Blueprint $table) {
            //
        });
    }
};
