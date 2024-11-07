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
        Schema::table('slot_waktu', function (Blueprint $table) {
            $table->boolean('is_istirahat')->default(false);
            $table->index('is_istirahat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slot_waktu', function (Blueprint $table) {
            $table->dropColumn('is_istirahat');
        });
    }
};
