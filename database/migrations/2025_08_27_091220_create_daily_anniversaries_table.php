<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_anniversaries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('years_ago');
            $table->date('original_date');
            $table->string('category');
            $table->string('type')->default('anniversary');
            $table->json('related_people')->nullable();
            $table->json('related_places')->nullable();
            $table->string('significance')->default('moderate');
            $table->boolean('is_recurring')->default(true);
            $table->json('celebration_info')->nullable();
            $table->timestamps();

            $table->index(['original_date']);
            $table->index(['category', 'significance']);
            $table->index(['is_recurring']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_anniversaries');
    }
};
