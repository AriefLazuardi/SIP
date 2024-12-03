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
        Schema::table('guru', function (Blueprint $table) {
            $table->string('tempat_lahir')->after('name');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('jabatan')->nullable()->after('total_jam_perminggu');
            $table->string('golongan')->nullable()->after('jabatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->dropColumn(['tempat_lahir', 'tanggal_lahir', 'jabatan', 'golongan']);
        });
    }
};
