<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $administrator = Role::create([
            'name' => 'administrator',
            'display_name' => 'Administrator', // optional
            'description' => 'User yang mengatur semuanya', // optional
        ]);

        $wakilkepalakurikulum = Role::create([
            'name' => 'Wakil Kepala Kurikulum',
            'display_name' => 'Waka Kurikulum', // optional
            'description' => 'User yang membuat jadwal', // optional
        ]);

        $guru = Role::create([
            'name' => 'Guru',
            'display_name' => 'Guru', // optional
            'description' => 'User yang melihat jadwal', // optional
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project', function (Blueprint $table) {
            //
        });
    }
};
