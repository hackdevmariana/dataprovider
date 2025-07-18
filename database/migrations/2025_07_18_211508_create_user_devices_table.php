<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_name')->nullable();
            $table->string('device_type')->nullable(); // 'mobile', 'tablet', 'desktop'
            $table->string('platform')->nullable();    // 'iOS', 'Android', 'Windows', etc.
            $table->string('browser')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('token')->nullable();       // token para notificaciones push
            $table->boolean('notifications_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
