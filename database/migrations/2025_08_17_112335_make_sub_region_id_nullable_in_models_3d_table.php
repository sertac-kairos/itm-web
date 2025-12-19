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
            $table->bigInteger('sub_region_id')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('models_3d', function (Blueprint $table) {
            $table->bigInteger('sub_region_id')->unsigned()->nullable(false)->change();
        });
    }
};
