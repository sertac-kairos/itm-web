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
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->foreignId('archaeological_site_id')->nullable()->after('sub_region_id')->constrained()->onDelete('cascade');
        });

        Schema::table('audio_guides', function (Blueprint $table) {
            $table->foreignId('archaeological_site_id')->nullable()->after('sub_region_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropForeign(['archaeological_site_id']);
            $table->dropColumn('archaeological_site_id');
        });

        Schema::table('audio_guides', function (Blueprint $table) {
            $table->dropForeign(['archaeological_site_id']);
            $table->dropColumn('archaeological_site_id');
        });
    }
};
