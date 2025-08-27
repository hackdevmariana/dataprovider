<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Campos básicos adicionales
            $table->string('slug')->nullable()->after('name');
            $table->string('genre')->nullable()->after('description');
            $table->date('formed_at')->nullable()->after('genre');
            $table->date('disbanded_at')->nullable()->after('formed_at');
            $table->string('active_status')->default('active')->after('disbanded_at');
            
            // Información de contacto y web
            $table->string('website')->nullable()->after('active_status');
            $table->json('social_media')->nullable()->after('website');
            $table->string('contact_email')->nullable()->after('social_media');
            $table->string('management_company')->nullable()->after('contact_email');
            
            // Lugar de origen
            $table->string('origin_country')->nullable()->after('management_company');
            $table->string('origin_city')->nullable()->after('origin_country');
            $table->string('current_location')->nullable()->after('origin_city');
            $table->foreignId('municipality_id')->nullable()->constrained('municipalities')->onDelete('set null')->after('current_location');
            
            // Información musical
            $table->string('record_label')->nullable()->after('municipality_id');
            $table->integer('albums_count')->default(0)->after('record_label');
            $table->integer('songs_count')->default(0)->after('albums_count');
            $table->json('awards')->nullable()->after('songs_count');
            $table->json('certifications')->nullable()->after('awards');
            
            // Contenido adicional
            $table->text('biography')->nullable()->after('certifications');
            $table->foreignId('image_id')->nullable()->constrained('images')->onDelete('set null')->after('biography');
            $table->json('tags')->nullable()->after('image_id');
            
            // Metadatos y configuración
            $table->decimal('search_boost', 3, 2)->default(1.00)->after('tags');
            $table->string('official_fan_club')->nullable()->after('search_boost');
            $table->boolean('is_verified')->default(false)->after('official_fan_club');
            $table->boolean('is_featured')->default(false)->after('is_verified');
            $table->string('source')->nullable()->after('is_featured');
            $table->json('metadata')->nullable()->after('source');
        });

        // Agregar índices
        Schema::table('groups', function (Blueprint $table) {
            $table->index(['slug']);
            $table->index(['genre', 'active_status']);
            $table->index(['origin_country']);
            $table->index(['is_verified', 'is_featured']);
            $table->index(['formed_at']);
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['genre', 'active_status']);
            $table->dropIndex(['origin_country']);
            $table->dropIndex(['is_verified', 'is_featured']);
            $table->dropIndex(['formed_at']);
            
            $table->dropForeign(['municipality_id']);
            $table->dropForeign(['image_id']);
            
            $table->dropColumn([
                'slug', 'genre', 'formed_at', 'disbanded_at', 'active_status',
                'website', 'social_media', 'contact_email', 'management_company',
                'origin_country', 'origin_city', 'current_location', 'municipality_id',
                'record_label', 'albums_count', 'songs_count', 'awards', 'certifications',
                'biography', 'image_id', 'tags', 'search_boost', 'official_fan_club',
                'is_verified', 'is_featured', 'source', 'metadata'
            ]);
        });
    }
};
