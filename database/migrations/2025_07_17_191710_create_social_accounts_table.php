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
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->enum('platform', ['twitter', 'instagram', 'youtube', 'tiktok', 'other']);
            $table->string('handle');
            $table->string('url');
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->integer('followers_count')->nullable();
            $table->boolean('verified')->default(false);
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
