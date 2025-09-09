<?php

namespace Database\Seeders;

use App\Models\EnergyService;
use App\Models\EnergyCompany;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EnergyServiceSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Verificar que existan empresas energéticas
        if (EnergyCompany::count() === 0) {
            $this->command->warn('No hay empresas energéticas. Creando algunas empresas de ejemplo...');
            EnergyCompany::factory(10)->create();
        }

        $companies = EnergyCompany::all();

        // Crear servicios energéticos aleatorios
        $this->command->info('Creando servicios energéticos...');

        // Servicios básicos (80% de los servicios)
        EnergyService::factory(80)
            ->create([
                'company_id' => fn() => $companies->random()->id,
            ]);

        // Servicios destacados (10% de los servicios)
        EnergyService::factory(10)
            ->featured()
            ->create([
                'company_id' => fn() => $companies->random()->id,
            ]);

        // Servicios populares (5% de los servicios)
        EnergyService::factory(5)
            ->popular()
            ->create([
                'company_id' => fn() => $companies->random()->id,
            ]);

        // Servicios de alta demanda (3% de los servicios)
        EnergyService::factory(3)
            ->highDemand()
            ->create([
                'company_id' => fn() => $companies->random()->id,
            ]);

        // Servicios no disponibles (2% de los servicios)
        EnergyService::factory(2)
            ->unavailable()
            ->create([
                'company_id' => fn() => $companies->random()->id,
            ]);

        // Servicios renovables especializados
        EnergyService::factory(15)
            ->renewable()
            ->create([
                'company_id' => fn() => $companies->random()->id,
            ]);

        // Servicios de hogar inteligente
        EnergyService::factory(8)
            ->smartHome()
            ->create([
                'company_id' => fn() => $companies->random()->id,
            ]);

        // Servicios de vehículo eléctrico
        EnergyService::factory(6)
            ->electricVehicle()
            ->create([
                'company_id' => fn() => $companies->random()->id,
            ]);

        // Crear servicios específicos para empresas conocidas
        $this->createSpecificServices($companies);

        $this->command->info('✅ Servicios energéticos creados exitosamente!');
    }

    private function createSpecificServices($companies): void
    {
        // Servicios para Iberdrola
        $iberdrola = $companies->where('name', 'like', '%Iberdrola%')->first();
        if ($iberdrola) {
            EnergyService::create([
                'company_id' => $iberdrola->id,
                'service_name' => 'Suministro Verde Residencial',
                'description' => 'Suministro de electricidad 100% renovable para hogares con certificación de origen garantizada.',
                'service_type' => 'supply',
                'energy_source' => 'renewable',
                'features' => [
                    'Electricidad 100% renovable',
                    'Certificación de origen',
                    'Precio fijo por 12 meses',
                    'App móvil incluida',
                    'Compensación por excedentes'
                ],
                'requirements' => [
                    'Contador inteligente',
                    'Conexión a red eléctrica',
                    'Documentación de identidad'
                ],
                'base_price' => 45.50,
                'pricing_model' => 'fixed',
                'pricing_details' => [
                    'Precio fijo mensual',
                    'Sin variaciones estacionales',
                    'Garantía de precio por 12 meses'
                ],
                'contract_duration' => '1 año',
                'terms_conditions' => [
                    'Duración mínima 12 meses',
                    'Cancelación con 30 días de antelación',
                    'Garantía de suministro 24/7'
                ],
                'is_available' => true,
                'is_featured' => true,
                'popularity_score' => 850,
            ]);

            EnergyService::create([
                'company_id' => $iberdrola->id,
                'service_name' => 'Instalación Solar Doméstica',
                'description' => 'Instalación completa de paneles solares con baterías de almacenamiento para autoconsumo.',
                'service_type' => 'installation',
                'energy_source' => 'solar',
                'features' => [
                    'Paneles solares de alta eficiencia',
                    'Sistema de baterías incluido',
                    'Instalación profesional',
                    'Garantía de 25 años',
                    'Monitoreo en tiempo real'
                ],
                'requirements' => [
                    'Tejado con orientación sur',
                    'Superficie mínima 20m²',
                    'Estructura de soporte adecuada'
                ],
                'base_price' => 8500.00,
                'pricing_model' => 'contract',
                'pricing_details' => [
                    'Precio por instalación completa',
                    'Financiación disponible',
                    'Subvenciones incluidas'
                ],
                'contract_duration' => 'Por proyecto',
                'terms_conditions' => [
                    'Instalación en 30 días',
                    'Garantía de funcionamiento',
                    'Mantenimiento incluido 2 años'
                ],
                'is_available' => true,
                'is_featured' => true,
                'popularity_score' => 920,
            ]);
        }

        // Servicios para Endesa
        $endesa = $companies->where('name', 'like', '%Endesa%')->first();
        if ($endesa) {
            EnergyService::create([
                'company_id' => $endesa->id,
                'service_name' => 'Hogar Inteligente Endesa',
                'description' => 'Solución completa de domótica para optimizar el consumo energético del hogar.',
                'service_type' => 'smart_home',
                'energy_source' => 'electricity',
                'features' => [
                    'Control inteligente de dispositivos',
                    'Optimización automática del consumo',
                    'Integración con renovables',
                    'App móvil avanzada',
                    'Asistente de voz incluido'
                ],
                'requirements' => [
                    'Conexión WiFi estable',
                    'Dispositivos compatibles',
                    'Smartphone o tablet'
                ],
                'base_price' => 29.90,
                'pricing_model' => 'subscription',
                'pricing_details' => [
                    'Pago mensual recurrente',
                    'Actualizaciones incluidas',
                    'Soporte técnico 24/7'
                ],
                'contract_duration' => 'Indefinido',
                'terms_conditions' => [
                    'Cancelación en cualquier momento',
                    'Garantía de funcionamiento',
                    'Actualizaciones automáticas'
                ],
                'is_available' => true,
                'is_featured' => true,
                'popularity_score' => 780,
            ]);
        }

        // Servicios para Naturgy
        $naturgy = $companies->where('name', 'like', '%Naturgy%')->first();
        if ($naturgy) {
            EnergyService::create([
                'company_id' => $naturgy->id,
                'service_name' => 'Gas Natural Premium',
                'description' => 'Suministro de gas natural con tarifas competitivas y servicio al cliente premium.',
                'service_type' => 'supply',
                'energy_source' => 'gas',
                'features' => [
                    'Tarifas competitivas',
                    'Servicio al cliente 24/7',
                    'Facturación digital',
                    'Mantenimiento incluido',
                    'Garantía de suministro'
                ],
                'requirements' => [
                    'Conexión a red de gas',
                    'Contador de gas',
                    'Documentación de identidad'
                ],
                'base_price' => 35.00,
                'pricing_model' => 'variable',
                'pricing_details' => [
                    'Precio según mercado',
                    'Actualización mensual',
                    'Transparencia total'
                ],
                'contract_duration' => '1 año',
                'terms_conditions' => [
                    'Duración mínima 12 meses',
                    'Cancelación con 30 días',
                    'Garantía de suministro'
                ],
                'is_available' => true,
                'is_featured' => false,
                'popularity_score' => 650,
            ]);
        }

        // Servicios para Repsol
        $repsol = $companies->where('name', 'like', '%Repsol%')->first();
        if ($repsol) {
            EnergyService::create([
                'company_id' => $repsol->id,
                'service_name' => 'Carga de Vehículo Eléctrico',
                'description' => 'Red de puntos de carga rápida para vehículos eléctricos con tecnología de última generación.',
                'service_type' => 'electric_vehicle',
                'energy_source' => 'electricity',
                'features' => [
                    'Carga rápida hasta 150kW',
                    'Red nacional de puntos',
                    'App de localización',
                    'Pago móvil integrado',
                    'Energía 100% renovable'
                ],
                'requirements' => [
                    'Vehículo eléctrico compatible',
                    'App móvil instalada',
                    'Cuenta de usuario activa'
                ],
                'base_price' => 0.35,
                'pricing_model' => 'pay_per_use',
                'pricing_details' => [
                    'Pago por kWh consumido',
                    'Sin costes fijos',
                    'Descuentos por volumen'
                ],
                'contract_duration' => 'Indefinido',
                'terms_conditions' => [
                    'Uso bajo demanda',
                    'Pago inmediato',
                    'Sin permanencia'
                ],
                'is_available' => true,
                'is_featured' => true,
                'popularity_score' => 720,
            ]);
        }

        // Servicios para EDP
        $edp = $companies->where('name', 'like', '%EDP%')->first();
        if ($edp) {
            EnergyService::create([
                'company_id' => $edp->id,
                'service_name' => 'Auditoría Energética Empresarial',
                'description' => 'Análisis completo del consumo energético empresarial con recomendaciones de optimización.',
                'service_type' => 'consulting',
                'energy_source' => 'all',
                'features' => [
                    'Análisis completo del consumo',
                    'Recomendaciones personalizadas',
                    'Plan de optimización',
                    'Seguimiento continuo',
                    'Certificación energética'
                ],
                'requirements' => [
                    'Acceso a instalaciones',
                    'Historial de consumos',
                    'Personal de contacto'
                ],
                'base_price' => 2500.00,
                'pricing_model' => 'contract',
                'pricing_details' => [
                    'Precio por auditoría completa',
                    'Informe detallado incluido',
                    'Seguimiento de 6 meses'
                ],
                'contract_duration' => '6 meses',
                'terms_conditions' => [
                    'Auditoría en 30 días',
                    'Informe en 15 días',
                    'Garantía de ahorro mínimo 15%'
                ],
                'is_available' => true,
                'is_featured' => false,
                'popularity_score' => 580,
            ]);
        }
    }
}
