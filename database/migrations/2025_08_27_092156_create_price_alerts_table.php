<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('energy_type')->default('electricity');
            $table->string('zone')->default('peninsula');
            $table->string('alert_type')->default('price_drop');
            $table->decimal('threshold_price', 8, 4);
            $table->string('condition')->default('below');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered')->nullable();
            $table->integer('trigger_count')->default(0);
            $table->json('notification_settings')->nullable();
            $table->string('frequency')->default('once');
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['energy_type', 'zone']);
            $table->index(['threshold_price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_alerts');
    }
};
