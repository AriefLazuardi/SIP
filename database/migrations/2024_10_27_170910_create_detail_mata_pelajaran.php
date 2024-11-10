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
        Schema::create('detail_mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mata_pelajaran_id');
            $table->unsignedBigInteger('tingkatan_kelas_id');
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->integer('total_jam_perminggu');
            $table->timestamps();

            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajaran')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tingkatan_kelas_id')->references('id')->on('tingkatan_kelas')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_mata_pelajaran');
    }
};
