<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('festival_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained()->onDelete('cascade');
            $table->date('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('event_name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('artist_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('group_id')->nullable()->constrained()->onDelete('set null');
            $table->string('event_type')->default('performance');
            $table->boolean('is_free')->default(true);
            $table->decimal('ticket_price', 8, 2)->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('current_attendance')->default(0);
            $table->json('additional_info')->nullable();
            $table->timestamps();

            $table->index(['festival_id', 'day']);
            $table->index(['start_time', 'end_time']);
            $table->index(['event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('festival_programs');
    }
};
