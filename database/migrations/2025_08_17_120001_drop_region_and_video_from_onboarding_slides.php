<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('onboarding_slides', function (Blueprint $table) {
            if (Schema::hasColumn('onboarding_slides', 'region_id')) {
                $table->dropConstrainedForeignId('region_id');
            }
            if (Schema::hasColumn('onboarding_slides', 'video')) {
                $table->dropColumn('video');
            }
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_slides', function (Blueprint $table) {
            if (!Schema::hasColumn('onboarding_slides', 'region_id')) {
                $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete()->after('id');
            }
            if (!Schema::hasColumn('onboarding_slides', 'video')) {
                $table->string('video')->nullable()->after('image');
            }
        });
    }
};


