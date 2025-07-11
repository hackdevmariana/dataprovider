<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('tag_type', ['topic', 'style', 'theme', 'mood'])->default('topic');
            $table->boolean('is_searchable')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tags');
    }
};
