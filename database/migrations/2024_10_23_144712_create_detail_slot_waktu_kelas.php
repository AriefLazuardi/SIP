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
        Schema::create('detail_slot_waktu_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tingkatan_kelas_id')->constrained('tingkatan_kelas')->onDelete('cascade');
            $table->integer('total_slot_waktu_perminggu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_slot_waktu_kelas');
    }
};
