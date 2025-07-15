<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();

            // Contenido básico
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('source_url');

            // Fechas
            $table->timestamp('published_at')->nullable();
            $table->timestamp('featured_start')->nullable();
            $table->timestamp('featured_end')->nullable();

            // Relaciones
            $table->foreignId('media_outlet_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->nullable()->constrained('people')->nullOnDelete();
            $table->foreignId('municipality_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('language_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('image_id')->nullable()->constrained('images')->nullOnDelete();
            $table->foreignId('tag_id')->nullable()->constrained('tags')->nullOnDelete();

            // Clasificación
            $table->boolean('is_outstanding')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_scraped')->default(true);
            $table->boolean('is_translated')->default(false);
            $table->enum('visibility', ['public', 'private'])->default('public');

            // SEO y control
            $table->unsignedInteger('views_count')->default(0);
            $table->json('tags')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
