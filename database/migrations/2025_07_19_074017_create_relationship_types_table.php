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
        Schema::create('relationship_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('slug', 40)->unique();
            $table->string('reciprocal_slug', 40)->nullable();
            $table->enum('category', ['family', 'legal', 'sentimental', 'otro'])->default('family');
            $table->unsignedTinyInteger('degree')->nullable();
            $table->boolean('gender_specific')->default(false);
            $table->string('description', 255)->nullable();
            $table->boolean('is_symmetrical')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relationship_types');
    }
};
