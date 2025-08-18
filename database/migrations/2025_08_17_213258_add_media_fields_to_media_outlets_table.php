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
        Schema::table('media_outlets', function (Blueprint $table) {
            // Información extendida
            $table->enum('media_category', ['diario', 'revista', 'digital', 'agencia', 'television', 'radio', 'blog'])->after('type')->default('digital');
            $table->text('description')->after('media_category')->nullable();
            $table->string('rss_feed')->after('website')->nullable();
            
            // Cobertura y audiencia
            $table->enum('coverage_scope', ['local', 'regional', 'nacional', 'internacional'])->after('municipality_id')->nullable();
            $table->json('languages')->after('coverage_scope')->nullable(); // Cambiar de string a JSON
            $table->enum('circulation_type', ['impreso', 'digital', 'mixto', 'audiencia'])->after('circulation')->nullable();
            
            // Propietario y orientación
            $table->string('owner_company')->after('founding_year')->nullable();
            $table->enum('political_leaning', ['izquierda', 'centro-izquierda', 'centro', 'centro-derecha', 'derecha', 'neutral'])->after('owner_company')->nullable();
            $table->json('specializations')->after('political_leaning')->nullable();
            
            // Características del medio
            $table->boolean('is_digital_native')->after('specializations')->default(false);
            $table->boolean('is_verified')->after('is_digital_native')->default(false);
            $table->boolean('is_active')->after('is_verified')->default(true);
            $table->boolean('covers_sustainability')->after('is_active')->default(false);
            
            // Métricas de calidad
            $table->decimal('credibility_score', 3, 1)->after('covers_sustainability')->nullable(); // 0-10
            $table->decimal('influence_score', 3, 1)->after('credibility_score')->nullable(); // 0-10
            $table->decimal('sustainability_focus', 3, 2)->after('influence_score')->nullable(); // 0-1
            
            // Métricas de audiencia
            $table->integer('articles_count')->after('sustainability_focus')->default(0);
            $table->integer('monthly_pageviews')->after('articles_count')->default(0);
            $table->integer('social_media_followers')->after('monthly_pageviews')->default(0);
            $table->json('social_media_handles')->after('social_media_followers')->nullable();
            
            // Contacto y prensa
            $table->string('contact_email')->after('social_media_handles')->nullable();
            $table->string('press_contact_name')->after('contact_email')->nullable();
            $table->string('press_contact_email')->after('press_contact_name')->nullable();
            $table->string('press_contact_phone')->after('press_contact_email')->nullable();
            $table->json('editorial_team')->after('press_contact_phone')->nullable();
            
            // Licencias y permisos
            $table->string('content_licensing')->after('editorial_team')->nullable();
            $table->boolean('allows_reprints')->after('content_licensing')->default(false);
            $table->json('api_access')->after('allows_reprints')->nullable();
            
            // Recursos adicionales
            $table->string('logo_url')->after('api_access')->nullable();
            
            // Timestamps especializados
            $table->timestamp('last_scraped_at')->after('logo_url')->nullable();
            $table->timestamp('verified_at')->after('last_scraped_at')->nullable();
            
            // Índices para optimización
            $table->index(['type', 'is_active']);
            $table->index(['coverage_scope', 'is_verified']);
            $table->index(['covers_sustainability', 'sustainability_focus']);
            $table->index(['credibility_score']);
            $table->index(['influence_score']);
            $table->index(['is_digital_native', 'founding_year']);
            $table->index(['municipality_id', 'coverage_scope']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_outlets', function (Blueprint $table) {
            // Eliminar índices primero
            $table->dropIndex(['type', 'is_active']);
            $table->dropIndex(['coverage_scope', 'is_verified']);
            $table->dropIndex(['covers_sustainability', 'sustainability_focus']);
            $table->dropIndex(['credibility_score']);
            $table->dropIndex(['influence_score']);
            $table->dropIndex(['is_digital_native', 'founding_year']);
            $table->dropIndex(['municipality_id', 'coverage_scope']);
            
            // Eliminar columnas
            $table->dropColumn([
                'media_category',
                'description',
                'rss_feed',
                'coverage_scope',
                'languages', // Nota: esto revertirá a string original
                'circulation_type',
                'owner_company',
                'political_leaning',
                'specializations',
                'is_digital_native',
                'is_verified',
                'is_active',
                'covers_sustainability',
                'credibility_score',
                'influence_score',
                'sustainability_focus',
                'articles_count',
                'monthly_pageviews',
                'social_media_followers',
                'social_media_handles',
                'contact_email',
                'press_contact_name',
                'press_contact_email',
                'press_contact_phone',
                'editorial_team',
                'content_licensing',
                'allows_reprints',
                'api_access',
                'logo_url',
                'last_scraped_at',
                'verified_at',
            ]);
        });
    }
};