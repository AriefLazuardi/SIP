<?php

use App\Models\Warna;
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

        $orangeColor = Warna::create([
            'kode_hex' => '#FF6600',
        ]);

        $amazonOrange = Warna::create([
            'kode_hex' => '#FF9900',
        ]);

        $brilliantYellow = Warna::create([
            'kode_hex' => '#FDD808',
        ]);

        $limeGreen = Warna::create([
            'kode_hex' => '#71E200',
        ]);

        $golfGreen = Warna::create([
            'kode_hex' => '#008854',
        ]);

        $alpineGreen = Warna::create([
            'kode_hex' => '#00CC99',
        ]);

        $skypeBlue = Warna::create([
            'kode_hex' => '#00AFF0',
        ]);

        $blueScreen = Warna::create([
            'kode_hex' => '#150DF7',
        ]);

        $kitchenBlue = Warna::create([
            'kode_hex' => '#8AB5BD',
        ]);

        $brickRed = Warna::create([
            'kode_hex' => '#C63C5F',
        ]);

        $candyPurple = Warna::create([
            'kode_hex' => '#A15BE4',
        ]);

        $darkBluePurple = Warna::create([
            'kode_hex' => '#66648B',
        ]);

        $deepPink = Warna::create([
            'kode_hex' => '#FF1493',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('color', function (Blueprint $table) {
            //
        });
    }
};
