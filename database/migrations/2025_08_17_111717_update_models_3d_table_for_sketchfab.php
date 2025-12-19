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
            $table->dropColumn(['model_path', 'preview_image']);
            $table->string('sketchfab_model_id')->after('archaeological_site_id');
            $table->string('sketchfab_thumbnail_url')->nullable()->after('sketchfab_model_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('models_3d', function (Blueprint $table) {
            $table->dropColumn(['sketchfab_model_id', 'sketchfab_thumbnail_url']);
            $table->string('model_path')->nullable()->after('archaeological_site_id');
            $table->string('preview_image')->nullable()->after('model_path');
        });
    }
};
