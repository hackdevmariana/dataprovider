<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('type')->default('website');
            $table->decimal('reliability_score', 3, 2)->default(5.00);
            $table->string('update_frequency')->default('daily');
            $table->timestamp('last_scraped')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('categories');
            $table->string('geographic_scope')->default('local');
            $table->string('language')->default('es');
            $table->json('api_credentials')->nullable();
            $table->json('scraping_rules')->nullable();
            $table->integer('articles_per_day')->nullable();
            $table->timestamp('last_error')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique('url');
            $table->index(['type', 'is_active']);
            $table->index(['reliability_score']);
            $table->index(['geographic_scope', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_sources');
    }
};
