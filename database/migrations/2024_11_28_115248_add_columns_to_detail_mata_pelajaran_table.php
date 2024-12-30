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
        Schema::table('detail_mata_pelajaran', function (Blueprint $table) {
            $table->unsignedBigInteger('kurikulum_id')->nullable(); // Kolom baru bisa nullable dulu
            $table->foreign('kurikulum_id')->references('id')->on('kurikulum')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_mata_pelajaran', function (Blueprint $table) {
            $table->dropForeign(['kurikulum_id']);
            $table->dropColumn('kurikulum_id');
        });
    }
};
