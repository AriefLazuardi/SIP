<?php

use App\Models\SesiBelajar;
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
        $sesiSatu = SesiBelajar::create([
            'name' => "Pagi",
        ]);
        $sesiDua = SesiBelajar::create([
            'name' => "Siang",
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_belajar', function (Blueprint $table) {
            //
        });
    }
};
