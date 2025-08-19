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
        Schema::create('roof_marketplace', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); // Propietario del techo
            $table->foreignId('municipality_id')->nullable()->constrained();
            
            // Información básica del espacio
            $table->string('title'); // Título del anuncio
            $table->string('slug')->unique(); // URL amigable
            $table->text('description'); // Descripción detallada
            $table->enum('space_type', [
                'residential_roof',      // Techo residencial
                'commercial_roof',       // Techo comercial
                'industrial_roof',       // Techo industrial
                'agricultural_land',     // Terreno agrícola
                'parking_lot',          // Aparcamiento
                'warehouse_roof',       // Techo de almacén
                'community_space',      // Espacio comunitario
                'unused_land',          // Terreno sin uso
                'building_facade',      // Fachada de edificio
                'other'                 // Otro
            ]);
            
            // Ubicación detallada
            $table->string('address'); // Dirección completa
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('postal_code', 10)->nullable();
            $table->text('access_instructions')->nullable(); // Instrucciones de acceso
            $table->json('nearby_landmarks')->nullable(); // Puntos de referencia cercanos
            
            // Características físicas del espacio
            $table->decimal('total_area_m2', 8, 2); // Área total en m²
            $table->decimal('usable_area_m2', 8, 2); // Área utilizable en m²
            $table->decimal('max_installable_power_kw', 8, 2)->nullable(); // Potencia máxima instalable
            $table->enum('roof_orientation', [
                'north', 'northeast', 'east', 'southeast',
                'south', 'southwest', 'west', 'northwest',
                'flat', 'multiple', 'optimal'
            ])->nullable();
            
            $table->integer('roof_inclination_degrees')->nullable(); // Inclinación del techo
            $table->enum('roof_material', [
                'tile', 'metal', 'concrete', 'asphalt',
                'slate', 'wood', 'membrane', 'other'
            ])->nullable();
            
            $table->enum('roof_condition', [
                'excellent', 'good', 'fair', 'needs_repair', 'poor'
            ])->nullable();
            
            $table->integer('roof_age_years')->nullable(); // Edad del techo
            $table->decimal('max_load_capacity_kg_m2', 8, 2)->nullable(); // Capacidad de carga
            
            // Condiciones ambientales
            $table->decimal('annual_solar_irradiation_kwh_m2', 8, 2)->nullable(); // Irradiación solar anual
            $table->integer('annual_sunny_days')->nullable(); // Días soleados al año
            $table->json('shading_analysis')->nullable(); // Análisis de sombreado
            $table->boolean('has_shading_issues')->default(false); // Problemas de sombreado
            $table->text('shading_description')->nullable(); // Descripción del sombreado
            
            // Acceso y logística
            $table->enum('access_difficulty', [
                'easy', 'moderate', 'difficult', 'very_difficult'
            ])->nullable();
            $table->text('access_description')->nullable(); // Descripción del acceso
            $table->boolean('crane_access')->default(false); // Acceso para grúa
            $table->boolean('vehicle_access')->default(false); // Acceso para vehículos
            $table->decimal('distance_to_electrical_panel_m', 6, 2)->nullable(); // Distancia al cuadro eléctrico
            
            // Aspectos legales y administrativos
            $table->boolean('has_building_permits')->default(false); // Tiene permisos de obra
            $table->boolean('community_approval_required')->default(false); // Requiere aprobación comunidad
            $table->boolean('community_approval_obtained')->default(false); // Aprobación obtenida
            $table->json('required_permits')->nullable(); // Permisos requeridos
            $table->json('obtained_permits')->nullable(); // Permisos obtenidos
            $table->text('legal_restrictions')->nullable(); // Restricciones legales
            
            // Términos comerciales
            $table->enum('offering_type', [
                'rent',                  // Alquiler del espacio
                'sale',                  // Venta del espacio
                'partnership',           // Sociedad/participación
                'free_use',              // Uso gratuito
                'energy_share',          // Participación en energía
                'mixed'                  // Términos mixtos
            ]);
            
            $table->decimal('monthly_rent_eur', 8, 2)->nullable(); // Alquiler mensual
            $table->decimal('sale_price_eur', 10, 2)->nullable(); // Precio de venta
            $table->decimal('energy_share_percentage', 5, 2)->nullable(); // % participación energía
            $table->integer('contract_duration_years')->nullable(); // Duración del contrato
            $table->boolean('renewable_contract')->default(true); // Contrato renovable
            $table->json('additional_terms')->nullable(); // Términos adicionales
            
            // Servicios incluidos
            $table->boolean('includes_maintenance')->default(false); // Incluye mantenimiento
            $table->boolean('includes_insurance')->default(false); // Incluye seguro
            $table->boolean('includes_permits_management')->default(false); // Gestión permisos
            $table->boolean('includes_monitoring')->default(false); // Monitorización incluida
            $table->json('included_services')->nullable(); // Servicios incluidos
            $table->json('additional_costs')->nullable(); // Costes adicionales
            
            // Estado y disponibilidad
            $table->enum('availability_status', [
                'available',             // Disponible
                'under_negotiation',     // En negociación
                'reserved',              // Reservado
                'contracted',            // Contratado
                'occupied',              // Ocupado
                'maintenance',           // En mantenimiento
                'temporarily_unavailable', // Temporalmente no disponible
                'withdrawn'              // Retirado
            ])->default('available');
            
            $table->date('available_from')->nullable(); // Disponible desde
            $table->date('available_until')->nullable(); // Disponible hasta
            $table->text('availability_notes')->nullable(); // Notas sobre disponibilidad
            
            // Información del propietario
            $table->boolean('owner_lives_onsite')->default(false); // Propietario vive en el sitio
            $table->enum('owner_involvement', [
                'none', 'minimal', 'moderate', 'active', 'full_partnership'
            ])->default('minimal');
            $table->json('owner_preferences')->nullable(); // Preferencias del propietario
            $table->text('owner_requirements')->nullable(); // Requisitos del propietario
            
            // Métricas y engagement
            $table->integer('views_count')->default(0);
            $table->integer('inquiries_count')->default(0);
            $table->integer('bookmarks_count')->default(0);
            $table->decimal('rating', 3, 1)->nullable(); // Rating promedio
            $table->integer('reviews_count')->default(0);
            
            // Documentación
            $table->json('images')->nullable(); // Imágenes del espacio
            $table->json('documents')->nullable(); // Documentos relacionados
            $table->json('technical_reports')->nullable(); // Informes técnicos
            $table->json('solar_analysis_reports')->nullable(); // Informes de análisis solar
            
            // Configuración
            $table->boolean('is_active')->default(true); // Anuncio activo
            $table->boolean('is_featured')->default(false); // Anuncio destacado
            $table->boolean('is_verified')->default(false); // Espacio verificado
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->boolean('auto_respond_inquiries')->default(false); // Respuesta automática
            $table->text('auto_response_message')->nullable(); // Mensaje automático
            
            $table->timestamps();
            
            $table->index(['municipality_id', 'space_type']);
            $table->index(['availability_status', 'is_active']);
            $table->index(['offering_type', 'usable_area_m2']);
            $table->index(['is_featured', 'views_count']);
            $table->index(['rating', 'reviews_count']);
            $table->fullText(['title', 'description', 'address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roof_marketplace');
    }
};
