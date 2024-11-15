<?php

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
        Schema::create('slot_waktu_tingkatan_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_waktu_id')->constrained('slot_waktu')->onDelete('cascade');
            $table->foreignId('tingkatan_kelas_id')->constrained('tingkatan_kelas')->onDelete('cascade');
            $table->foreignId('sesi_belajar_id')->constrained('sesi_belajar')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slot_waktu_tingkatan_kelas');
    }
};
