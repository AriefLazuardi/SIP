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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slot_waktu_mapping_id');
            $table->unsignedBigInteger('kelas_tugas_mengajar_id');
            $table->unsignedBigInteger('ruangan_id');
            $table->unsignedBigInteger('tahun_ajaran_id');

            $table->foreign('slot_waktu_mapping_id')->references('id')->on('slot_waktu_mapping')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('kelas_tugas_mengajar_id')->references('id')->on('kelas_tugas_mengajar')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('ruangan_id')->references('id')->on('ruangan')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
