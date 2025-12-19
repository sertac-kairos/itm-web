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
        Schema::table('models_3d', function (Blueprint $table) {
            $table->uuid('qr_uuid')->nullable()->after('sketchfab_thumbnail_url')->unique();
            $table->string('qr_image_path')->nullable()->after('qr_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('models_3d', function (Blueprint $table) {
            $table->dropUnique(['qr_uuid']);
            $table->dropColumn(['qr_uuid', 'qr_image_path']);
        });
    }
};
