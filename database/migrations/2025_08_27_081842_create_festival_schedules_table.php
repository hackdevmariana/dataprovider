<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('festival_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('opening_time');
            $table->time('closing_time');
            $table->json('main_events');
            $table->json('side_activities')->nullable();
            $table->text('special_notes')->nullable();
            $table->string('weather_forecast')->nullable();
            $table->integer('expected_attendance')->nullable();
            $table->json('transportation_info')->nullable();
            $table->json('parking_info')->nullable();
            $table->timestamps();

            $table->index(['festival_id', 'date']);
            $table->index(['opening_time', 'closing_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('festival_schedules');
    }
};
