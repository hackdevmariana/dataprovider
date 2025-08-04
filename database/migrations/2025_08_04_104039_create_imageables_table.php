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
        Schema::create('imageables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_id')->constrained()->onDelete('cascade');
            $table->morphs('imageable'); // Esto crea imageable_type (string) + imageable_id (unsignedBigInteger)
            $table->string('type')->nullable(); // opcional: avatar, logo, banner, etc.
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imageables');
    }
};
