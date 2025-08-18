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
        Schema::table('news_articles', function (Blueprint $table) {
            // Contenido extendido
            $table->text('excerpt')->after('content')->nullable();
            $table->string('original_title')->after('title')->nullable();
            
            // Categorización y clasificación
            $table->string('category')->after('image_id')->default('general');
            $table->string('topic_focus')->after('category')->nullable();
            $table->enum('article_type', ['noticia', 'reportaje', 'entrevista', 'opinion', 'analisis', 'comunicado'])->after('topic_focus')->default('noticia');
            
            // Flags adicionales
            $table->boolean('is_breaking_news')->after('is_translated')->default(false);
            $table->boolean('is_evergreen')->after('is_breaking_news')->default(false);
            
            // Estado del artículo
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->after('is_evergreen')->default('draft');
            
            // Métricas de engagement
            $table->integer('shares_count')->after('views_count')->default(0);
            $table->integer('comments_count')->after('shares_count')->default(0);
            $table->decimal('reading_time_minutes', 5, 2)->after('comments_count')->nullable();
            $table->integer('word_count')->after('reading_time_minutes')->nullable();
            
            // Análisis de sentimiento
            $table->decimal('sentiment_score', 3, 2)->after('word_count')->nullable(); // -1 a 1
            $table->enum('sentiment_label', ['positivo', 'neutral', 'negativo'])->after('sentiment_score')->nullable();
            
            // Metadatos estructurados
            $table->json('keywords')->after('sentiment_label')->nullable();
            $table->json('entities')->after('keywords')->nullable();
            
            // Sostenibilidad y medio ambiente
            $table->json('sustainability_topics')->after('entities')->nullable();
            $table->decimal('environmental_impact_score', 3, 1)->after('sustainability_topics')->nullable(); // 0-10
            $table->json('related_co2_data')->after('environmental_impact_score')->nullable();
            
            // Geolocalización
            $table->string('geo_scope')->after('related_co2_data')->nullable(); // local, regional, nacional, internacional
            $table->decimal('latitude', 10, 6)->after('geo_scope')->nullable();
            $table->decimal('longitude', 10, 6)->after('latitude')->nullable();
            
            // SEO y redes sociales
            $table->string('seo_title')->after('longitude')->nullable();
            $table->text('seo_description')->after('seo_title')->nullable();
            $table->json('social_media_meta')->after('seo_description')->nullable();
            
            // Timestamps adicionales
            $table->timestamp('scraped_at')->after('social_media_meta')->nullable();
            $table->timestamp('last_engagement_at')->after('scraped_at')->nullable();
            
            // Índices para optimización
            $table->index(['status', 'published_at']);
            $table->index(['category', 'published_at']);
            $table->index(['is_outstanding', 'featured_start', 'featured_end']);
            $table->index(['is_breaking_news', 'published_at']);
            $table->index(['media_outlet_id', 'published_at']);
            $table->index(['municipality_id', 'published_at']);
            $table->index(['environmental_impact_score']);
            $table->index(['views_count']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_articles', function (Blueprint $table) {
            // Eliminar índices primero
            $table->dropIndex(['status', 'published_at']);
            $table->dropIndex(['category', 'published_at']);
            $table->dropIndex(['is_outstanding', 'featured_start', 'featured_end']);
            $table->dropIndex(['is_breaking_news', 'published_at']);
            $table->dropIndex(['media_outlet_id', 'published_at']);
            $table->dropIndex(['municipality_id', 'published_at']);
            $table->dropIndex(['environmental_impact_score']);
            $table->dropIndex(['views_count']);
            $table->dropIndex(['latitude', 'longitude']);
            
            // Eliminar columnas
            $table->dropColumn([
                'excerpt',
                'original_title',
                'category',
                'topic_focus',
                'article_type',
                'is_breaking_news',
                'is_evergreen',
                'status',
                'shares_count',
                'comments_count',
                'reading_time_minutes',
                'word_count',
                'sentiment_score',
                'sentiment_label',
                'keywords',
                'entities',
                'sustainability_topics',
                'environmental_impact_score',
                'related_co2_data',
                'geo_scope',
                'latitude',
                'longitude',
                'seo_title',
                'seo_description',
                'social_media_meta',
                'scraped_at',
                'last_engagement_at',
            ]);
        });
    }
};