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
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('genre')->nullable();
            $table->foreignId('person_id')->nullable()->constrained();
            $table->string('stage_name')->nullable();
            $table->string('group_name')->nullable();
            $table->year('active_years_start')->nullable();
            $table->year('active_years_end')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->json('social_links')->nullable();
            $table->foreignId('language_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artists');
    }
};
