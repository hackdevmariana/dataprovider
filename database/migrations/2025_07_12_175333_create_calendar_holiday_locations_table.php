<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_holiday_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_holiday_id')->constrained()->onDelete('cascade');
            $table->foreignId('municipality_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('province_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('autonomous_community_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_holiday_locations');
    }
};
