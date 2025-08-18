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
        Schema::create('content_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Quien vota
            $table->morphs('votable'); // votable_type + votable_id (post, comment, project, etc.)
            
            $table->enum('vote_type', ['upvote', 'downvote']);
            $table->integer('vote_weight')->default(1); // Peso del voto (usuarios con más reputación = más peso)
            
            // Contexto del voto
            $table->string('reason')->nullable(); // Razón del downvote
            $table->boolean('is_helpful_vote')->default(false); // Si es un voto "útil"
            $table->json('metadata')->nullable(); // Datos adicionales
            
            // Validación
            $table->boolean('is_valid')->default(true); // Si el voto es válido
            $table->foreignId('validated_by')->nullable()->constrained('users');
            
            $table->timestamps();
            
            $table->unique(['user_id', 'votable_type', 'votable_id']); // Un voto por usuario por contenido
            $table->index(['votable_type', 'votable_id', 'vote_type']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_votes');
    }
};
