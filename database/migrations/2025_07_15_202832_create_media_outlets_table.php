<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media_outlets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['newspaper', 'tv', 'radio', 'blog', 'magazine']);
            $table->string('website')->nullable();
            $table->string('headquarters_location')->nullable();
            $table->foreignId('municipality_id')->nullable()->constrained();
            $table->string('language')->nullable();
            $table->integer('circulation')->nullable();
            $table->year('founding_year')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_outlets');
    }
};
