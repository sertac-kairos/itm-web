<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add logo settings to app_settings table
        DB::table('app_settings')->insert([
            [
                'key' => 'izmir_kalkinma_ajansi_logo',
                'value' => null,
                'description' => 'İzmir Kalkınma Ajansı Logo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'sanayi_teknoloji_bakanligi_logo',
                'value' => null,
                'description' => 'Sanayi ve Teknoloji Bakanlığı Logo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'hafiza_izmir_logo',
                'value' => null,
                'description' => 'Hafıza İzmir Logo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove logo settings from app_settings table
        DB::table('app_settings')->whereIn('key', [
            'izmir_kalkinma_ajansi_logo',
            'sanayi_teknoloji_bakanligi_logo',
            'hafiza_izmir_logo'
        ])->delete();
    }
};
