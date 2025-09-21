<?php

namespace Database\Seeders;

use App\Models\RoofMarketplace;
use App\Models\User;
use App\Models\Municipality;
use Illuminate\Database\Seeder;

class RoofMarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏠 Sembrando marketplace de tejados...');

        // Obtener datos necesarios
        $users = User::take(25)->get();
        $municipalities = Municipality::take(15)->get();

        if ($users->isEmpty()) {
            $this->command->error('❌ No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        if ($municipalities->isEmpty()) {
            $this->command->error('❌ No hay municipios disponibles. Ejecuta MunicipalitySeeder primero.');
            return;
        }

        $spaceTypes = ['residential_roof', 'commercial_roof', 'industrial_roof', 'agricultural_land', 'parking_lot', 'warehouse_roof', 'community_space', 'unused_land', 'building_facade', 'other'];
        $roofOrientations = ['south', 'southeast', 'southwest', 'east', 'west'];
        $roofMaterials = ['tile', 'metal', 'concrete', 'asphalt', 'slate', 'wood', 'membrane', 'other'];
        $roofConditions = ['excellent', 'good', 'fair', 'needs_repair', 'poor'];
        $accessDifficulties = ['easy', 'moderate', 'difficult', 'very_difficult'];
        $offeringTypes = ['rent', 'sale', 'partnership', 'free_use', 'energy_share', 'mixed'];
        $availabilityStatuses = ['available', 'under_negotiation', 'reserved', 'contracted', 'occupied', 'maintenance', 'temporarily_unavailable', 'withdrawn'];

        $createdCount = 0;

        foreach ($users as $user) {
            // Cada usuario puede tener entre 0 y 3 ofertas
            $numListings = fake()->numberBetween(0, 3);
            
            for ($i = 0; $i < $numListings; $i++) {
                $municipality = $municipalities->random();
                $spaceType = fake()->randomElement($spaceTypes);
                $roofOrientation = fake()->randomElement($roofOrientations);
                $roofMaterial = fake()->randomElement($roofMaterials);
                $roofCondition = fake()->randomElement($roofConditions);
                $accessDifficulty = fake()->randomElement($accessDifficulties);
                $offeringType = fake()->randomElement($offeringTypes);
                $availabilityStatus = fake()->randomElement($availabilityStatuses);

                $totalArea = fake()->randomFloat(2, 50, 2000);
                $usableArea = $totalArea * fake()->randomFloat(2, 0.6, 1.0);
                $maxInstallablePower = $usableArea * fake()->randomFloat(2, 0.15, 0.25); // kW por m²

                $listing = RoofMarketplace::create([
                    'owner_id' => $user->id,
                    'municipality_id' => $municipality->id,
                    'title' => $this->generateTitle($spaceType, $municipality->name),
                    'slug' => fake()->slug(),
                    'description' => $this->generateDescription($spaceType, $municipality->name, $totalArea),
                    'space_type' => $spaceType,
                    'address' => fake()->streetAddress(),
                    'latitude' => fake()->latitude(36, 44), // España
                    'longitude' => fake()->longitude(-9, 4), // España
                    'postal_code' => fake()->postcode(),
                    'access_instructions' => $this->generateAccessInstructions($accessDifficulty),
                    'nearby_landmarks' => json_encode($this->generateLandmarks()),
                    'total_area_m2' => $totalArea,
                    'usable_area_m2' => $usableArea,
                    'max_installable_power_kw' => $maxInstallablePower,
                    'roof_orientation' => $roofOrientation,
                    'roof_inclination_degrees' => fake()->numberBetween(15, 45),
                    'roof_material' => $roofMaterial,
                    'roof_condition' => $roofCondition,
                    'roof_age_years' => fake()->numberBetween(0, 50),
                    'max_load_capacity_kg_m2' => fake()->randomFloat(2, 50, 200),
                    'annual_solar_irradiation_kwh_m2' => fake()->randomFloat(2, 1200, 1800),
                    'annual_sunny_days' => fake()->numberBetween(250, 320),
                    'shading_analysis' => json_encode($this->generateShadingAnalysis()),
                    'has_shading_issues' => fake()->boolean(30),
                    'shading_description' => fake()->boolean(30) ? fake()->paragraph() : null,
                    'access_difficulty' => $accessDifficulty,
                    'access_description' => $this->generateAccessDescription($accessDifficulty),
                    'crane_access' => fake()->boolean(60),
                    'vehicle_access' => fake()->boolean(80),
                    'distance_to_electrical_panel_m' => fake()->randomFloat(2, 5, 200),
                    'has_building_permits' => fake()->boolean(70),
                    'community_approval_required' => fake()->boolean(40),
                    'community_approval_obtained' => fake()->boolean(30),
                    'required_permits' => json_encode($this->generateRequiredPermits()),
                    'obtained_permits' => json_encode($this->generateObtainedPermits()),
                    'legal_restrictions' => fake()->boolean(20) ? fake()->paragraph() : null,
                    'offering_type' => $offeringType,
                    'monthly_rent_eur' => $offeringType === 'rent' ? fake()->randomFloat(2, 100, 2000) : null,
                    'sale_price_eur' => $offeringType === 'sale' ? fake()->randomFloat(2, 50000, 500000) : null,
                    'energy_share_percentage' => $offeringType === 'energy_share' ? fake()->randomFloat(2, 20, 80) : null,
                    'contract_duration_years' => fake()->numberBetween(10, 25),
                    'renewable_contract' => fake()->boolean(60),
                    'additional_terms' => json_encode($this->generateAdditionalTerms($offeringType)),
                    'includes_maintenance' => fake()->boolean(50),
                    'includes_insurance' => fake()->boolean(40),
                    'includes_permits_management' => fake()->boolean(60),
                    'includes_monitoring' => fake()->boolean(70),
                    'included_services' => json_encode($this->generateIncludedServices()),
                    'additional_costs' => json_encode($this->generateAdditionalCosts()),
                    'availability_status' => $availabilityStatus,
                    'available_from' => fake()->dateTimeBetween('-1 month', '+3 months'),
                    'available_until' => fake()->boolean(70) ? fake()->dateTimeBetween('+1 year', '+5 years') : null,
                    'availability_notes' => fake()->boolean(40) ? fake()->sentence() : null,
                    'owner_lives_onsite' => fake()->boolean(60),
                    'owner_involvement' => fake()->randomElement(['none', 'minimal', 'moderate', 'active', 'full_partnership']),
                    'owner_preferences' => json_encode($this->generateOwnerPreferences()),
                    'owner_requirements' => fake()->boolean(60) ? fake()->paragraph() : null,
                    'views_count' => fake()->numberBetween(0, 500),
                    'inquiries_count' => fake()->numberBetween(0, 50),
                    'bookmarks_count' => fake()->numberBetween(0, 25),
                    'rating' => fake()->randomFloat(1, 3.0, 5.0),
                    'reviews_count' => fake()->numberBetween(0, 20),
                    'images' => json_encode($this->generateImages($spaceType)),
                    'documents' => json_encode($this->generateDocuments()),
                    'technical_reports' => json_encode($this->generateTechnicalReports()),
                    'solar_analysis_reports' => json_encode($this->generateSolarAnalysisReports()),
                    'is_active' => fake()->boolean(85),
                    'is_featured' => fake()->boolean(15),
                    'is_verified' => fake()->boolean(40),
                    'verified_by' => fake()->boolean(40) ? $users->random()->id : null,
                    'verified_at' => fake()->boolean(40) ? fake()->dateTimeBetween('-6 months', 'now') : null,
                    'auto_respond_inquiries' => fake()->boolean(30),
                    'auto_response_message' => fake()->boolean(30) ? fake()->sentence() : null,
                ]);

                $createdCount++;
            }
        }

        $this->command->info("✅ Creadas {$createdCount} ofertas en el marketplace de tejados");
        $this->showStatistics();
    }

    private function generateTitle(string $spaceType, string $municipalityName): string
    {
        return match($spaceType) {
            'residential_roof' => fake()->randomElement([
                "Tejado residencial ideal para instalación solar en {$municipalityName}",
                "Superficie de tejado residencial disponible - {$municipalityName}",
                "Espacio en tejado residencial para energía solar - {$municipalityName}"
            ]),
            'commercial_roof' => fake()->randomElement([
                "Tejado comercial disponible para parque solar en {$municipalityName}",
                "Superficie de tejado comercial para instalación fotovoltaica - {$municipalityName}",
                "Tejado comercial ideal para proyecto solar - {$municipalityName}"
            ]),
            'industrial_roof' => fake()->randomElement([
                "Tejado industrial disponible para integración solar en {$municipalityName}",
                "Superficie de tejado industrial para paneles solares - {$municipalityName}",
                "Tejado industrial solar integrado - {$municipalityName}"
            ]),
            'agricultural_land' => fake()->randomElement([
                "Terreno agrícola disponible para parque solar en {$municipalityName}",
                "Superficie agrícola para instalación fotovoltaica - {$municipalityName}",
                "Terreno agrícola ideal para proyecto solar - {$municipalityName}"
            ]),
            'parking_lot' => fake()->randomElement([
                "Aparcamiento con cubierta solar en {$municipalityName}",
                "Parking con instalación fotovoltaica - {$municipalityName}",
                "Cubierta de aparcamiento solar - {$municipalityName}"
            ]),
            'warehouse_roof' => fake()->randomElement([
                "Nave industrial con tejado solar en {$municipalityName}",
                "Almacén con instalación fotovoltaica - {$municipalityName}",
                "Nave con cubierta solar - {$municipalityName}"
            ]),
            default => "Espacio disponible para energía solar en {$municipalityName}"
        };
    }

    private function generateDescription(string $spaceType, string $municipalityName, float $totalArea): string
    {
        $areaText = number_format($totalArea, 0, ',', '.') . ' m²';
        
        return match($spaceType) {
            'residential_roof' => "Excelente oportunidad para instalar paneles solares en un tejado residencial de {$areaText} " .
                     "ubicado en {$municipalityName}. El tejado tiene una orientación sur óptima y " .
                     "se encuentra en excelente estado. Ideal para proyectos de autoconsumo o " .
                     "generación de energía renovable.",
                     
            'commercial_roof' => "Tejado comercial de {$areaText} perfecto para la instalación de un parque solar " .
                       "en {$municipalityName}. La superficie es plana y tiene acceso directo a " .
                       "infraestructuras eléctricas. Ideal para proyectos de gran escala.",
                       
            'industrial_roof' => "Tejado industrial de {$areaText} disponible para la integración de paneles solares " .
                       "en {$municipalityName}. Orientación sur con excelente exposición solar " .
                       "durante todo el día.",
                       
            'agricultural_land' => "Terreno agrícola de {$areaText} con estructura ideal para " .
                        "instalación de paneles solares en {$municipalityName}. Doble beneficio: " .
                        "cultivo y generación de energía limpia.",
                        
            'parking_lot' => "Aparcamiento cubierto de {$areaText} con estructura ideal para " .
                        "instalación de paneles solares en {$municipalityName}. Doble beneficio: " .
                        "sombra para vehículos y generación de energía limpia.",
                        
            'warehouse_roof' => "Nave industrial de {$areaText} con tejado robusto perfecto para " .
                          "instalación fotovoltaica en {$municipalityName}. Estructura preparada " .
                          "para soportar paneles solares de gran capacidad.",
                          
            default => "Espacio de {$areaText} disponible para proyecto solar en {$municipalityName}."
        };
    }

    private function generateAccessInstructions(string $accessDifficulty): string
    {
        return match($accessDifficulty) {
            'easy' => "Acceso directo desde la carretera principal. Aparcamiento disponible en la propiedad. " .
                     "No se requiere equipamiento especial.",
                     
            'moderate' => "Acceso por carretera secundaria en buen estado. Aparcamiento limitado disponible. " .
                         "Se recomienda vehículo de tamaño estándar.",
                         
            'difficult' => "Acceso por camino rural. Se requiere vehículo todo terreno o 4x4. " .
                          "Aparcamiento limitado. Coordinar visita previa.",
                          
            'very_difficult' => "Acceso complicado por terreno irregular. Se requiere coordinación especial " .
                               "y equipamiento adecuado. Contactar para planificar visita.",
                               
            default => "Acceso estándar. Contactar para más detalles."
        };
    }

    private function generateLandmarks(): array
    {
        $landmarks = [];
        $numLandmarks = fake()->numberBetween(1, 3);
        
        for ($i = 0; $i < $numLandmarks; $i++) {
            $landmarks[] = fake()->randomElement([
                fake()->streetName() . ' (a 200m)',
                'Centro comercial ' . fake()->company() . ' (a 500m)',
                'Estación de tren (a 1km)',
                'Hospital ' . fake()->company() . ' (a 800m)',
                'Parque ' . fake()->firstName() . ' (a 300m)',
                'Escuela ' . fake()->company() . ' (a 600m)'
            ]);
        }
        
        return $landmarks;
    }

    private function generateShadingAnalysis(): array
    {
        return [
            'morning_shading' => fake()->randomFloat(2, 0, 30),
            'afternoon_shading' => fake()->randomFloat(2, 0, 25),
            'winter_shading' => fake()->randomFloat(2, 5, 40),
            'summer_shading' => fake()->randomFloat(2, 0, 20),
            'nearby_buildings' => fake()->numberBetween(0, 3),
            'trees_nearby' => fake()->numberBetween(0, 5),
            'shading_impact_score' => fake()->randomFloat(1, 1, 5)
        ];
    }

    private function generateAccessDescription(string $accessDifficulty): string
    {
        return match($accessDifficulty) {
            'easy' => "Acceso directo y sin complicaciones. Carretera asfaltada hasta la propiedad.",
            'moderate' => "Acceso por carretera secundaria. Algunas curvas pronunciadas pero transitable.",
            'difficult' => "Acceso por camino rural. Requiere conducción cuidadosa y vehículo adecuado.",
            'very_difficult' => "Acceso complicado. Terreno irregular y camino en mal estado.",
            default => "Acceso estándar a la propiedad."
        };
    }

    private function generateRequiredPermits(): array
    {
        $permits = ['building_permit', 'electrical_permit'];
        
        if (fake()->boolean(60)) {
            $permits[] = 'environmental_permit';
        }
        
        if (fake()->boolean(40)) {
            $permits[] = 'urban_planning_permit';
        }
        
        if (fake()->boolean(30)) {
            $permits[] = 'heritage_permit';
        }
        
        return $permits;
    }

    private function generateObtainedPermits(): array
    {
        $requiredPermits = $this->generateRequiredPermits();
        $obtainedPermits = [];
        
        foreach ($requiredPermits as $permit) {
            if (fake()->boolean(70)) {
                $obtainedPermits[] = $permit;
            }
        }
        
        return $obtainedPermits;
    }

    private function generateAdditionalTerms(string $offeringType): array
    {
        $terms = [];
        
        if ($offeringType === 'rent') {
            $terms['rent_increase'] = fake()->randomFloat(2, 0, 5) . '% anual';
            $terms['minimum_contract'] = fake()->numberBetween(10, 20) . ' años';
        }
        
        if ($offeringType === 'energy_share') {
            $terms['energy_share_percentage'] = fake()->randomFloat(2, 20, 80) . '%';
            $terms['price_per_kwh'] = fake()->randomFloat(4, 0.05, 0.15) . ' €/kWh';
        }
        
        $terms['maintenance_responsibility'] = fake()->randomElement(['owner', 'tenant', 'shared']);
        $terms['insurance_coverage'] = fake()->boolean(80) ? 'Incluida' : 'Por cuenta del inquilino';
        
        return $terms;
    }

    private function generateIncludedServices(): array
    {
        $services = [];
        
        if (fake()->boolean(60)) $services[] = 'maintenance';
        if (fake()->boolean(40)) $services[] = 'monitoring';
        if (fake()->boolean(50)) $services[] = 'insurance';
        if (fake()->boolean(30)) $services[] = 'cleaning';
        if (fake()->boolean(20)) $services[] = 'performance_optimization';
        
        return $services;
    }

    private function generateAdditionalCosts(): array
    {
        $costs = [];
        
        if (fake()->boolean(30)) {
            $costs['connection_fee'] = fake()->randomFloat(2, 500, 5000) . ' €';
        }
        
        if (fake()->boolean(20)) {
            $costs['permits_fee'] = fake()->randomFloat(2, 200, 2000) . ' €';
        }
        
        if (fake()->boolean(25)) {
            $costs['maintenance_deposit'] = fake()->randomFloat(2, 1000, 10000) . ' €';
        }
        
        return $costs;
    }

    private function generateOwnerPreferences(): array
    {
        return [
            'preferred_installer_type' => fake()->randomElement(['certified', 'local', 'international']),
            'communication_preference' => fake()->randomElement(['email', 'phone', 'in_person']),
            'visit_schedule' => fake()->randomElement(['weekdays', 'weekends', 'flexible']),
            'project_size_preference' => fake()->randomElement(['small', 'medium', 'large', 'any']),
            'environmental_commitment' => fake()->boolean(80)
        ];
    }

    private function generateImages(string $spaceType): array
    {
        if (!fake()->boolean(80)) {
            return [];
        }

        $imageCount = fake()->numberBetween(2, 6);
        $images = [];
        
        for ($i = 0; $i < $imageCount; $i++) {
            $images[] = [
                'url' => fake()->imageUrl(1200, 800, match($spaceType) {
                    'residential_roof' => 'building',
                    'commercial_roof' => 'building',
                    'industrial_roof' => 'building',
                    'agricultural_land' => 'nature',
                    'parking_lot' => 'transport',
                    'warehouse_roof' => 'business',
                    default => 'city'
                }),
                'alt' => fake()->sentence(3),
                'type' => fake()->randomElement(['overview', 'detail', 'access', 'surroundings']),
                'uploaded_at' => fake()->dateTimeBetween('-1 month', 'now')
            ];
        }
        
        return $images;
    }

    private function generateDocuments(): array
    {
        if (!fake()->boolean(60)) {
            return [];
        }

        $documents = [];
        
        if (fake()->boolean(80)) {
            $documents[] = [
                'name' => 'Escritura de propiedad',
                'type' => 'property_deed',
                'url' => fake()->url()
            ];
        }
        
        if (fake()->boolean(60)) {
            $documents[] = [
                'name' => 'Certificado energético',
                'type' => 'energy_certificate',
                'url' => fake()->url()
            ];
        }
        
        if (fake()->boolean(40)) {
            $documents[] = [
                'name' => 'Planos del edificio',
                'type' => 'building_plans',
                'url' => fake()->url()
            ];
        }
        
        return $documents;
    }

    private function generateTechnicalReports(): array
    {
        if (!fake()->boolean(50)) {
            return [];
        }

        return [
            [
                'name' => 'Estudio de viabilidad técnica',
                'type' => 'feasibility_study',
                'url' => fake()->url(),
                'generated_at' => fake()->dateTimeBetween('-6 months', 'now')
            ],
            [
                'name' => 'Análisis estructural',
                'type' => 'structural_analysis',
                'url' => fake()->url(),
                'generated_at' => fake()->dateTimeBetween('-3 months', 'now')
            ]
        ];
    }

    private function generateSolarAnalysisReports(): array
    {
        if (!fake()->boolean(70)) {
            return [];
        }

        return [
            [
                'name' => 'Análisis de irradiación solar',
                'type' => 'irradiation_analysis',
                'url' => fake()->url(),
                'estimated_annual_production' => fake()->randomFloat(2, 1000, 50000) . ' kWh',
                'generated_at' => fake()->dateTimeBetween('-3 months', 'now')
            ]
        ];
    }

    private function showStatistics(): void
    {
        $total = RoofMarketplace::count();
        $active = RoofMarketplace::where('is_active', true)->count();
        $verified = RoofMarketplace::where('is_verified', true)->count();
        $featured = RoofMarketplace::where('is_featured', true)->count();
        
        $totalArea = RoofMarketplace::sum('total_area_m2');
        $totalPower = RoofMarketplace::sum('max_installable_power_kw');
        
        $bySpaceType = RoofMarketplace::selectRaw('space_type, COUNT(*) as count')
            ->groupBy('space_type')
            ->pluck('count', 'space_type');
        
        $byOfferingType = RoofMarketplace::selectRaw('offering_type, COUNT(*) as count')
            ->groupBy('offering_type')
            ->pluck('count', 'offering_type');
        
        $byAvailability = RoofMarketplace::selectRaw('availability_status, COUNT(*) as count')
            ->groupBy('availability_status')
            ->pluck('count', 'availability_status');

        $avgRating = RoofMarketplace::where('rating', '>', 0)->avg('rating');
        $totalViews = RoofMarketplace::sum('views_count');
        $totalInquiries = RoofMarketplace::sum('inquiries_count');

        $this->command->info("\n📊 Estadísticas del marketplace de tejados:");
        $this->command->info("   • Total de ofertas: {$total}");
        $this->command->info("   • Ofertas activas: {$active}");
        $this->command->info("   • Ofertas verificadas: {$verified}");
        $this->command->info("   • Ofertas destacadas: {$featured}");
        $this->command->info("   • Superficie total: " . number_format($totalArea, 0, ',', '.') . " m²");
        $this->command->info("   • Potencia total instalable: " . number_format($totalPower, 0, ',', '.') . " kW");
        $this->command->info("   • Valoración promedio: " . round($avgRating, 1) . "/5");
        $this->command->info("   • Total de visualizaciones: {$totalViews}");
        $this->command->info("   • Total de consultas: {$totalInquiries}");

        $this->command->info("\n🏠 Por tipo de espacio:");
        foreach ($bySpaceType as $type => $count) {
            $typeLabel = match($type) {
                'residential_roof' => 'Tejado Residencial',
                'commercial_roof' => 'Tejado Comercial',
                'industrial_roof' => 'Tejado Industrial',
                'agricultural_land' => 'Terreno Agrícola',
                'parking_lot' => 'Aparcamiento',
                'warehouse_roof' => 'Nave',
                'community_space' => 'Espacio Comunitario',
                'unused_land' => 'Terreno Sin Uso',
                'building_facade' => 'Fachada',
                'other' => 'Otro',
                default => ucfirst($type)
            };
            $this->command->info("   • {$typeLabel}: {$count}");
        }

        $this->command->info("\n💰 Por tipo de oferta:");
        foreach ($byOfferingType as $type => $count) {
            $typeLabel = match($type) {
                'rent' => 'Alquiler',
                'sale' => 'Venta',
                'partnership' => 'Colaboración',
                'free_use' => 'Uso Gratuito',
                'energy_share' => 'Compartir energía',
                'mixed' => 'Mixto',
                default => ucfirst($type)
            };
            $this->command->info("   • {$typeLabel}: {$count}");
        }

        $this->command->info("\n📅 Por disponibilidad:");
        foreach ($byAvailability as $status => $count) {
            $statusLabel = match($status) {
                'available' => 'Disponible',
                'under_negotiation' => 'En negociación',
                'reserved' => 'Reservado',
                'contracted' => 'Contratado',
                'occupied' => 'Ocupado',
                'maintenance' => 'En mantenimiento',
                'temporarily_unavailable' => 'Temporalmente no disponible',
                'withdrawn' => 'Retirado',
                default => ucfirst($status)
            };
            $this->command->info("   • {$statusLabel}: {$count}");
        }
    }
}







