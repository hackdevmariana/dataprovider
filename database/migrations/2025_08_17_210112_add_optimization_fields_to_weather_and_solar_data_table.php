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
        Schema::table('weather_and_solar_data', function (Blueprint $table) {
            // Ubicación mejorada
            $table->foreignId('municipality_id')->after('location')->nullable()->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 6)->after('municipality_id')->nullable();
            $table->decimal('longitude', 10, 6)->after('latitude')->nullable();
            
            // Temperatura extendida
            $table->decimal('temperature_min', 5, 2)->after('temperature')->nullable();
            $table->decimal('temperature_max', 5, 2)->after('temperature_min')->nullable();
            
            // Datos solares mejorados
            $table->decimal('solar_irradiance_daily', 8, 3)->after('solar_irradiance')->nullable();
            $table->decimal('uv_index', 4, 1)->after('solar_irradiance_daily')->nullable();
            
            // Datos de viento extendidos
            $table->decimal('wind_direction', 5, 1)->after('wind_speed')->nullable();
            $table->decimal('wind_gust', 5, 2)->after('wind_direction')->nullable();
            
            // Datos atmosféricos adicionales
            $table->decimal('pressure', 7, 2)->after('precipitation')->nullable();
            $table->decimal('visibility', 5, 1)->after('pressure')->nullable();
            $table->string('weather_condition')->after('visibility')->nullable();
            
            // Metadatos
            $table->enum('data_type', ['historical', 'current', 'forecast'])->after('weather_condition')->default('current');
            $table->string('source')->after('data_type')->default('manual');
            $table->string('source_url')->after('source')->nullable();
            
            // Potenciales calculados
            $table->decimal('solar_potential', 8, 3)->after('source_url')->nullable();
            $table->decimal('wind_potential', 8, 3)->after('solar_potential')->nullable();
            
            // Indicadores de optimización
            $table->boolean('is_optimal_solar')->after('wind_potential')->default(false);
            $table->boolean('is_optimal_wind')->after('is_optimal_solar')->default(false);
            
            // Calidad del aire
            $table->integer('air_quality_index')->after('is_optimal_wind')->nullable();
            
            // Índices para consultas eficientes
            $table->index(['datetime', 'data_type']);
            $table->index(['municipality_id', 'datetime']);
            $table->index(['is_optimal_solar', 'datetime']);
            $table->index(['is_optimal_wind', 'datetime']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weather_and_solar_data', function (Blueprint $table) {
            // Eliminar índices primero
            $table->dropIndex(['datetime', 'data_type']);
            $table->dropIndex(['municipality_id', 'datetime']);
            $table->dropIndex(['is_optimal_solar', 'datetime']);
            $table->dropIndex(['is_optimal_wind', 'datetime']);
            $table->dropIndex(['latitude', 'longitude']);
            
            // Eliminar columnas
            $table->dropForeign(['municipality_id']);
            $table->dropColumn([
                'municipality_id',
                'latitude',
                'longitude',
                'temperature_min',
                'temperature_max',
                'solar_irradiance_daily',
                'uv_index',
                'wind_direction',
                'wind_gust',
                'pressure',
                'visibility',
                'weather_condition',
                'data_type',
                'source',
                'source_url',
                'solar_potential',
                'wind_potential',
                'is_optimal_solar',
                'is_optimal_wind',
                'air_quality_index',
            ]);
        });
    }
};