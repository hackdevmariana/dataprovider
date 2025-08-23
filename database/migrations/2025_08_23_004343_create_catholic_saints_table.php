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
        Schema::create('catholic_saints', function (Blueprint $table) {
            $table->id();
            
            // Información básica del santo
            $table->string('name')->comment('Nombre del santo');
            $table->string('canonical_name')->nullable()->comment('Nombre canónico en latín');
            $table->string('slug')->unique()->comment('Slug único para URL');
            $table->text('description')->nullable()->comment('Descripción breve del santo');
            $table->longText('biography')->nullable()->comment('Biografía completa del santo');
            
            // Fechas importantes
            $table->date('birth_date')->nullable()->comment('Fecha de nacimiento');
            $table->date('death_date')->nullable()->comment('Fecha de muerte/tránsito');
            $table->date('canonization_date')->nullable()->comment('Fecha de canonización');
            $table->date('feast_date')->comment('Fecha de celebración litúrgica');
            $table->date('feast_date_optional')->nullable()->comment('Fecha alternativa de celebración');
            
            // Clasificación y categorías
            $table->enum('category', [
                'martyr',           // Mártir
                'confessor',        // Confesor
                'virgin',           // Virgen
                'virgin_martyr',    // Virgen y Mártir
                'bishop',           // Obispo
                'pope',             // Papa
                'religious',        // Religioso/a
                'lay_person',       // Laico/a
                'founder',          // Fundador/a
                'doctor',           // Doctor de la Iglesia
                'apostle',          // Apóstol
                'evangelist',       // Evangelista
                'prophet',          // Profeta
                'patriarch',        // Patriarca
                'other'             // Otros
            ])->default('other')->comment('Categoría del santo');
            
            $table->enum('feast_type', [
                'solemnity',        // Solemnidad
                'feast',            // Fiesta
                'memorial',         // Memoria
                'optional_memorial', // Memoria opcional
                'commemoration'     // Conmemoración
            ])->default('memorial')->comment('Tipo de celebración litúrgica');
            
            $table->enum('liturgical_color', [
                'white',            // Blanco
                'red',              // Rojo
                'green',            // Verde
                'purple',           // Morado
                'pink',             // Rosa
                'gold',             // Dorado
                'black'             // Negro
            ])->nullable()->comment('Color litúrgico de la celebración');
            
            // Patronazgos y especialidades
            $table->text('patron_of')->nullable()->comment('Patrono de (oficios, lugares, causas)');
            $table->boolean('is_patron')->default(false)->comment('Es patrono de algún lugar o causa');
            $table->json('patronages')->nullable()->comment('Lista de patronazgos específicos');
            $table->text('specialties')->nullable()->comment('Especialidades o virtudes del santo');
            
            // Información geográfica y cultural
            $table->unsignedBigInteger('birth_place_id')->nullable()->comment('Lugar de nacimiento');
            $table->unsignedBigInteger('death_place_id')->nullable()->comment('Lugar de muerte');
            $table->unsignedBigInteger('municipality_id')->nullable()->comment('Municipio donde es patrono');
            $table->string('region')->nullable()->comment('Región o territorio de influencia');
            $table->string('country')->nullable()->comment('País de origen o influencia');
            
            // Información litúrgica
            $table->string('liturgical_rank')->nullable()->comment('Rango litúrgico');
            $table->text('prayers')->nullable()->comment('Oraciones asociadas al santo');
            $table->text('hymns')->nullable()->comment('Himnos asociados al santo');
            $table->json('attributes')->nullable()->comment('Atributos o símbolos del santo');
            
            // Metadatos y estado
            $table->boolean('is_active')->default(true)->comment('Santo activo en el calendario');
            $table->boolean('is_universal')->default(true)->comment('Celebrado universalmente');
            $table->boolean('is_local')->default(false)->comment('Solo celebrado localmente');
            $table->integer('popularity_score')->default(0)->comment('Puntuación de popularidad');
            $table->text('notes')->nullable()->comment('Notas adicionales');
            
            $table->timestamps();
            
            // Índices para optimización
            $table->index(['feast_date', 'category']);
            $table->index(['municipality_id', 'is_patron']);
            $table->index(['is_active', 'is_universal']);
            $table->index('slug');
            
            // Claves foráneas
            $table->foreign('municipality_id')->references('id')->on('municipalities')->nullOnDelete();
            $table->foreign('birth_place_id')->references('id')->on('municipalities')->nullOnDelete();
            $table->foreign('death_place_id')->references('id')->on('municipalities')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catholic_saints');
    }
};
