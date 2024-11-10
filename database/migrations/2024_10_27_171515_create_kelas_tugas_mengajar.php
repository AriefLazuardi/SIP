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
        Schema::create('kelas_tugas_mengajar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tugas_mengajar_id');
            $table->unsignedBigInteger('kelas_id');
            
            $table->foreign('tugas_mengajar_id')->references('id')->on('tugas_mengajar')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_tugas_mengajar');
    }
};
