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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['electricity_price', 'event', 'solar_production']);
            $table->unsignedBigInteger('target_id')->nullable(); // e.g., municipality_id
            $table->decimal('threshold', 10, 4)->nullable();
            $table->enum('delivery_method', ['app', 'email', 'sms'])->default('app');
            $table->boolean('is_silent')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
