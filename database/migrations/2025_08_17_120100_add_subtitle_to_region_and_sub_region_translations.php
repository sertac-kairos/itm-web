<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('region_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('region_translations', 'subtitle')) {
                $table->string('subtitle')->nullable()->after('name');
            }
        });

        Schema::table('sub_region_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('sub_region_translations', 'subtitle')) {
                $table->string('subtitle')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('region_translations', function (Blueprint $table) {
            if (Schema::hasColumn('region_translations', 'subtitle')) {
                $table->dropColumn('subtitle');
            }
        });

        Schema::table('sub_region_translations', function (Blueprint $table) {
            if (Schema::hasColumn('sub_region_translations', 'subtitle')) {
                $table->dropColumn('subtitle');
            }
        });
    }
};


