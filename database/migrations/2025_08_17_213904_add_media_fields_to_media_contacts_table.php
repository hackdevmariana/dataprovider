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
        Schema::table('media_contacts', function (Blueprint $table) {
            // Información profesional extendida
            $table->string('job_title')->after('contact_name')->nullable();
            $table->string('department')->after('job_title')->nullable();
            $table->string('mobile_phone')->after('phone')->nullable();
            $table->string('secondary_email')->after('email')->nullable();
            
            // Especializaciones y cobertura
            $table->json('specializations')->after('secondary_email')->nullable();
            $table->json('coverage_areas')->after('specializations')->nullable();
            
            // Preferencias de contacto
            $table->enum('preferred_contact_method', ['email', 'phone', 'mobile_phone', 'social_media', 'whatsapp'])->after('coverage_areas')->nullable();
            $table->json('availability_schedule')->after('preferred_contact_method')->nullable();
            $table->string('language_preference')->after('availability_schedule')->default('es');
            
            // Aceptación de contenidos
            $table->boolean('accepts_press_releases')->after('language_preference')->default(true);
            $table->boolean('accepts_interviews')->after('accepts_press_releases')->default(true);
            $table->boolean('accepts_events_invitations')->after('accepts_interviews')->default(true);
            
            // Estado y tipo del contacto
            $table->boolean('is_freelancer')->after('accepts_events_invitations')->default(false);
            $table->boolean('is_active')->after('is_freelancer')->default(true);
            $table->boolean('is_verified')->after('is_active')->default(false);
            $table->integer('priority_level')->after('is_verified')->default(3); // 1-5
            
            // Métricas de interacción
            $table->decimal('response_rate', 3, 2)->after('priority_level')->nullable(); // 0-1
            $table->integer('contacts_count')->after('response_rate')->default(0);
            $table->integer('successful_contacts')->after('contacts_count')->default(0);
            
            // Información adicional
            $table->json('social_media_profiles')->after('successful_contacts')->nullable();
            $table->text('bio')->after('social_media_profiles')->nullable();
            $table->json('recent_articles')->after('bio')->nullable();
            $table->text('notes')->after('recent_articles')->nullable();
            $table->json('interaction_history')->after('notes')->nullable();
            
            // Timestamps especializados
            $table->timestamp('last_contacted_at')->after('interaction_history')->nullable();
            $table->timestamp('last_response_at')->after('last_contacted_at')->nullable();
            $table->timestamp('verified_at')->after('last_response_at')->nullable();
            
            // Índices para optimización
            $table->index(['media_outlet_id', 'type', 'is_active']);
            $table->index(['type', 'priority_level']);
            $table->index(['is_verified', 'is_active']);
            $table->index(['response_rate']);
            $table->index(['last_contacted_at']);
            $table->index(['is_freelancer', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_contacts', function (Blueprint $table) {
            // Eliminar índices primero
            $table->dropIndex(['media_outlet_id', 'type', 'is_active']);
            $table->dropIndex(['type', 'priority_level']);
            $table->dropIndex(['is_verified', 'is_active']);
            $table->dropIndex(['response_rate']);
            $table->dropIndex(['last_contacted_at']);
            $table->dropIndex(['is_freelancer', 'is_active']);
            
            // Eliminar columnas
            $table->dropColumn([
                'job_title',
                'department',
                'mobile_phone',
                'secondary_email',
                'specializations',
                'coverage_areas',
                'preferred_contact_method',
                'availability_schedule',
                'language_preference',
                'accepts_press_releases',
                'accepts_interviews',
                'accepts_events_invitations',
                'is_freelancer',
                'is_active',
                'is_verified',
                'priority_level',
                'response_rate',
                'contacts_count',
                'successful_contacts',
                'social_media_profiles',
                'bio',
                'recent_articles',
                'notes',
                'interaction_history',
                'last_contacted_at',
                'last_response_at',
                'verified_at',
            ]);
        });
    }
};