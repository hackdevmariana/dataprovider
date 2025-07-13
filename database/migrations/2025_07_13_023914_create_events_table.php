<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime')->nullable();
            $table->foreignId('venue_id')->nullable()->constrained();
            $table->foreignId('event_type_id')->nullable()->constrained();
            $table->foreignId('festival_id')->nullable()->constrained();
            $table->foreignId('language_id')->nullable()->constrained();
            $table->foreignId('timezone_id')->nullable()->constrained();
            $table->foreignId('municipality_id')->nullable()->constrained();
            $table->foreignId('point_of_interest_id')->nullable()->constrained();
            $table->foreignId('work_id')->nullable()->constrained();
            $table->decimal('price', 8, 2)->nullable();
            $table->boolean('is_free')->default(false);
            $table->unsignedInteger('audience_size_estimate')->nullable();
            $table->string('source_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
