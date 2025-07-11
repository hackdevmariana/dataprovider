<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('birth_name')->nullable();
            $table->string('slug')->unique();
            $table->date('birth_date')->nullable();
            $table->date('death_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('death_place')->nullable();
            $table->foreignId('nationality_id')->nullable()->constrained('countries');
            $table->foreignId('language_id')->nullable()->constrained('languages');
            $table->foreignId('image_id')->nullable()->constrained('images');
            $table->enum('gender', ['male', 'female', 'other'])->default('other');
            $table->string('official_website')->nullable();
            $table->string('wikidata_id')->nullable();
            $table->string('wikipedia_url')->nullable();
            $table->string('notable_for')->nullable();
            $table->string('occupation_summary')->nullable();
            $table->json('social_handles')->nullable();
            $table->boolean('is_influencer')->default(false);
            $table->integer('search_boost')->default(0);
            $table->text('short_bio')->nullable();
            $table->longText('long_bio')->nullable();
            $table->string('source_url')->nullable();
            $table->timestamp('last_updated_from_source')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('people');
    }
};
