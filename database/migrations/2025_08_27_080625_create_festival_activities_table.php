<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('festival_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type')->default('workshop');
            $table->text('description')->nullable();
            $table->time('start_time');
            $table->integer('duration_minutes');
            $table->string('location')->nullable();
            $table->string('organizer')->nullable();
            $table->integer('max_participants')->nullable();
            $table->string('age_restriction')->nullable();
            $table->json('requirements')->nullable();
            $table->json('materials_provided')->nullable();
            $table->boolean('requires_registration')->default(false);
            $table->decimal('participation_fee', 8, 2)->nullable();
            $table->timestamps();

            $table->index(['festival_id', 'type']);
            $table->index(['start_time']);
            $table->index(['requires_registration']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('festival_activities');
    }
};
