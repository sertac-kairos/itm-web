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
        // Add new site settings to app_settings table with English parameter names
        DB::table('app_settings')->insert([
            [
                'key' => 'time_travel_slider_active',
                'value' => '1',
                'description' => 'Time Travel Slider Active (bool)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'time_travel_slider_count',
                'value' => '5',
                'description' => 'Time Travel Slider Count (int)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'stories_active',
                'value' => '1',
                'description' => 'Stories Active (bool)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'nearby_archaeological_sites_active',
                'value' => '1',
                'description' => 'Nearby Archaeological Sites Active (bool)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'nearby_archaeological_sites_count',
                'value' => '10',
                'description' => 'Nearby Archaeological Sites Count (int)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'featured_articles_active',
                'value' => '1',
                'description' => 'Featured Articles Active (bool)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'memory_izmir_active',
                'value' => '1',
                'description' => 'Memory Izmir Active (bool)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'model_background_color',
                'value' => '#ffffff',
                'description' => 'Model Background Color (color string)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'time_travel_hotspot_image_visible',
                'value' => '1',
                'description' => 'Time Travel Hotspot Image Visible (bool)',
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
        // Remove new site settings from app_settings table
        DB::table('app_settings')->whereIn('key', [
            'time_travel_slider_active',
            'time_travel_slider_count',
            'stories_active',
            'nearby_archaeological_sites_active',
            'nearby_archaeological_sites_count',
            'featured_articles_active',
            'memory_izmir_active',
            'model_background_color',
            'time_travel_hotspot_image_visible'
        ])->delete();
    }
};