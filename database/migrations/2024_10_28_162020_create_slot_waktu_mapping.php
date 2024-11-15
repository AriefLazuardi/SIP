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
        Schema::create('slot_waktu_mapping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_waktu_tingkatan_kelas_id')->constrained('slot_waktu_tingkatan_kelas')->onDelete('cascade');
            $table->foreignId('hari_id')->constrained('hari')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slot_waktu_mapping');
    }
};
