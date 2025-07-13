<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('artist_group_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')->constrained();
            $table->foreignId('group_id')->constrained();
            $table->date('joined_at')->nullable();
            $table->date('left_at')->nullable();
            $table->timestamps();

            $table->unique(['artist_id', 'group_id']);
        });
    }

    public function down(): void
    {
        // Importante: eliminar primero la tabla pivote que depende de la otra
        Schema::dropIfExists('artist_group_member');
        Schema::dropIfExists('groups');
    }
};
