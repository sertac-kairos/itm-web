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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('gcm_id')->nullable(); // Google Cloud Messaging ID
            $table->string('device_id')->unique(); // Unique device identifier
            $table->decimal('latitude', 10, 8)->nullable(); // Latitude with precision
            $table->decimal('longitude', 11, 8)->nullable(); // Longitude with precision
            $table->string('platform')->nullable(); // iOS, Android, Web
            $table->string('app_version')->nullable(); // App version
            $table->string('os_version')->nullable(); // Operating system version
            $table->boolean('is_active')->default(true); // Device status
            $table->timestamp('last_seen')->nullable(); // Last activity timestamp
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('device_id');
            $table->index('is_active');
            $table->index('last_seen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
