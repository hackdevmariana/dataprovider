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
        Schema::table('groups', function (Blueprint $table) {
            // Información básica del grupo
            $table->string('slug')->nullable()->after('name');
            $table->string('genre')->nullable()->after('description');
            $table->date('formed_at')->nullable()->after('genre');
            $table->date('disbanded_at')->nullable()->after('formed_at');
            $table->enum('active_status', ['active', 'inactive', 'disbanded', 'on_hiatus'])->default('active')->after('disbanded_at');
            
            // Información de contacto y web
            $table->string('website')->nullable()->after('active_status');
            $table->json('social_media')->nullable()->after('website');
            $table->string('contact_email')->nullable()->after('social_media');
            $table->string('management_company')->nullable()->after('contact_email');
            
            // Información de origen y ubicación
            $table->string('origin_country')->nullable()->after('management_company');
            $table->string('origin_city')->nullable()->after('origin_country');
            $table->string('current_location')->nullable()->after('origin_city');
            $table->foreignId('municipality_id')->nullable()->after('current_location')->constrained()->nullOnDelete();
            
            // Información musical y comercial
            $table->string('record_label')->nullable()->after('municipality_id');
            $table->unsignedInteger('albums_count')->default(0)->after('record_label');
            $table->unsignedInteger('songs_count')->default(0)->after('albums_count');
            $table->json('awards')->nullable()->after('songs_count');
            $table->json('certifications')->nullable()->after('awards');
            
            // Metadatos y SEO
            $table->longText('biography')->nullable()->after('certifications');
            $table->foreignId('image_id')->nullable()->after('biography')->constrained('images')->nullOnDelete();
            $table->json('tags')->nullable()->after('image_id');
            $table->integer('search_boost')->default(100)->after('tags');
            
            // Información adicional
            $table->string('official_fan_club')->nullable()->after('search_boost');
            $table->boolean('is_verified')->default(false)->after('official_fan_club');
            $table->boolean('is_featured')->default(false)->after('is_verified');
            $table->string('source')->default('manual')->after('is_featured');
            $table->json('metadata')->nullable()->after('source');
            
            // Índices para mejorar el rendimiento
            $table->index(['genre', 'active_status']);
            $table->index(['origin_country', 'origin_city']);
            $table->index(['formed_at', 'disbanded_at']);
            $table->index(['is_verified', 'is_featured']);
            $table->index(['search_boost']);
            $table->index(['slug']);
        });

        // Generar slugs únicos para grupos existentes
        $this->generateSlugsForExistingGroups();

        // Hacer el campo slug único después de generar todos los slugs
        Schema::table('groups', function (Blueprint $table) {
            $table->string('slug')->unique()->change();
        });
    }

    /**
     * Generar slugs únicos para grupos existentes.
     */
    private function generateSlugsForExistingGroups(): void
    {
        $groups = \DB::table('groups')->whereNull('slug')->orWhere('slug', '')->get();
        
        foreach ($groups as $group) {
            $baseSlug = \Str::slug($group->name);
            $slug = $baseSlug;
            $counter = 1;
            
            // Verificar que el slug sea único
            while (\DB::table('groups')->where('slug', $slug)->where('id', '!=', $group->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            \DB::table('groups')->where('id', $group->id)->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex(['genre', 'active_status']);
            $table->dropIndex(['origin_country', 'origin_city']);
            $table->dropIndex(['formed_at', 'disbanded_at']);
            $table->dropIndex(['is_verified', 'is_featured']);
            $table->dropIndex(['search_boost']);
            $table->dropIndex(['slug']);
            
            // Eliminar foreign keys
            $table->dropForeign(['municipality_id']);
            $table->dropForeign(['image_id']);
            
            // Eliminar campos agregados
            $table->dropColumn([
                'slug', 'genre', 'formed_at', 'disbanded_at', 'active_status',
                'website', 'social_media', 'contact_email', 'management_company',
                'origin_country', 'origin_city', 'current_location', 'municipality_id',
                'record_label', 'albums_count', 'songs_count', 'awards', 'certifications',
                'biography', 'image_id', 'tags', 'search_boost',
                'official_fan_club', 'is_verified', 'is_featured', 'source', 'metadata'
            ]);
        });
    }
};
