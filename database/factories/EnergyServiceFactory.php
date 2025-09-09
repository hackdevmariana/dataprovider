<?php

namespace Database\Factories;

use App\Models\EnergyService;
use App\Models\EnergyCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EnergyService>
 */
class EnergyServiceFactory extends Factory
{
    protected $model = EnergyService::class;

    public function definition(): array
    {
        $serviceTypes = [
            'supply', 'distribution', 'generation', 'storage', 'consulting',
            'maintenance', 'installation', 'monitoring', 'billing', 'support',
            'energy_audit', 'efficiency', 'renewable', 'smart_home', 'electric_vehicle'
        ];

        $energySources = [
            'electricity', 'gas', 'oil', 'coal', 'solar', 'wind',
            'hydro', 'nuclear', 'biomass', 'geothermal', 'hybrid', 'all'
        ];

        $pricingModels = [
            'fixed', 'variable', 'tiered', 'subscription', 'pay_per_use',
            'contract', 'free', 'custom'
        ];

        $contractDurations = [
            '1 mes', '3 meses', '6 meses', '1 año', '2 años', '3 años',
            '5 años', '10 años', 'Indefinido', 'Por proyecto'
        ];

        $serviceType = $this->faker->randomElement($serviceTypes);
        $energySource = $this->faker->randomElement($energySources);
        $pricingModel = $this->faker->randomElement($pricingModels);

        // Generar características basadas en el tipo de servicio
        $features = $this->generateFeatures($serviceType);
        $requirements = $this->generateRequirements($serviceType);
        $pricingDetails = $this->generatePricingDetails($pricingModel);
        $termsConditions = $this->generateTermsConditions();

        return [
            'company_id' => EnergyCompany::factory(),
            'service_name' => $this->generateServiceName($serviceType, $energySource),
            'description' => $this->generateDescription($serviceType, $energySource),
            'service_type' => $serviceType,
            'energy_source' => $energySource,
            'features' => $features,
            'requirements' => $requirements,
            'base_price' => $this->faker->randomFloat(2, 0, 1000),
            'pricing_model' => $pricingModel,
            'pricing_details' => $pricingDetails,
            'contract_duration' => $this->faker->randomElement($contractDurations),
            'terms_conditions' => $termsConditions,
            'is_available' => $this->faker->boolean(85), // 85% disponibles
            'is_featured' => $this->faker->boolean(15), // 15% destacados
            'popularity_score' => $this->faker->numberBetween(0, 1000),
        ];
    }

    private function generateServiceName(string $serviceType, string $energySource): string
    {
        $serviceNames = [
            'supply' => [
                'Suministro Energético Premium',
                'Energía Verde Residencial',
                'Suministro Empresarial Eficiente',
                'Energía Renovable Doméstica',
                'Suministro Inteligente 24/7'
            ],
            'distribution' => [
                'Red de Distribución Inteligente',
                'Distribución Energética Optimizada',
                'Red Eléctrica de Alta Eficiencia',
                'Distribución Renovable',
                'Red Inteligente Avanzada'
            ],
            'generation' => [
                'Generación Solar Residencial',
                'Parque Eólico Comunitario',
                'Generación Hidroeléctrica',
                'Planta de Biomasa',
                'Generación Nuclear Segura'
            ],
            'storage' => [
                'Sistema de Almacenamiento Inteligente',
                'Baterías de Alta Capacidad',
                'Almacenamiento Energético Doméstico',
                'Sistema de Reserva Energética',
                'Almacenamiento Comunitario'
            ],
            'consulting' => [
                'Consultoría Energética Avanzada',
                'Asesoramiento en Eficiencia',
                'Consultoría Renovable',
                'Optimización Energética',
                'Auditoría Energética Profesional'
            ],
            'maintenance' => [
                'Mantenimiento Preventivo',
                'Servicio Técnico Especializado',
                'Mantenimiento Inteligente',
                'Servicio de Reparación 24/7',
                'Mantenimiento Predictivo'
            ],
            'installation' => [
                'Instalación Solar Completa',
                'Sistema Eólico Residencial',
                'Instalación de Baterías',
                'Sistema Híbrido Inteligente',
                'Instalación de Paneles Solares'
            ],
            'monitoring' => [
                'Monitoreo Energético Inteligente',
                'Sistema de Supervisión 24/7',
                'Monitoreo de Consumo',
                'Control Energético Avanzado',
                'Sistema de Alertas Inteligente'
            ],
            'billing' => [
                'Facturación Inteligente',
                'Gestión de Pagos Automática',
                'Facturación Transparente',
                'Sistema de Facturación Digital',
                'Gestión Financiera Energética'
            ],
            'support' => [
                'Soporte Técnico Especializado',
                'Atención al Cliente 24/7',
                'Soporte Energético Avanzado',
                'Asistencia Técnica Premium',
                'Soporte Personalizado'
            ]
        ];

        $names = $serviceNames[$serviceType] ?? ['Servicio Energético Premium'];
        return $this->faker->randomElement($names);
    }

    private function generateDescription(string $serviceType, string $energySource): string
    {
        $descriptions = [
            'supply' => 'Servicio de suministro energético que garantiza un suministro estable y eficiente para su hogar o empresa.',
            'distribution' => 'Sistema de distribución energética optimizado que maximiza la eficiencia y minimiza las pérdidas.',
            'generation' => 'Solución de generación energética que aprovecha fuentes renovables para producir energía limpia.',
            'storage' => 'Sistema de almacenamiento energético que permite gestionar y optimizar el consumo de energía.',
            'consulting' => 'Servicio de consultoría especializada para optimizar el consumo energético y reducir costos.',
            'maintenance' => 'Servicio de mantenimiento preventivo y correctivo para garantizar el funcionamiento óptimo.',
            'installation' => 'Instalación profesional de sistemas energéticos con garantía y soporte técnico completo.',
            'monitoring' => 'Sistema de monitoreo en tiempo real para supervisar y optimizar el rendimiento energético.',
            'billing' => 'Gestión completa de facturación energética con transparencia y facilidad de pago.',
            'support' => 'Soporte técnico especializado disponible las 24 horas para resolver cualquier incidencia.'
        ];

        $baseDescription = $descriptions[$serviceType] ?? 'Servicio energético profesional y confiable.';
        
        $energySourceText = match($energySource) {
            'electricity' => 'eléctrica',
            'gas' => 'de gas natural',
            'solar' => 'solar',
            'wind' => 'eólica',
            'hydro' => 'hidroeléctrica',
            'nuclear' => 'nuclear',
            'biomass' => 'de biomasa',
            'geothermal' => 'geotérmica',
            'hybrid' => 'híbrida',
            'all' => 'de múltiples fuentes',
            default => 'energética'
        };

        return $baseDescription . ' Especializado en energía ' . $energySourceText . '.';
    }

    private function generateFeatures(string $serviceType): array
    {
        $featureSets = [
            'supply' => [
                'Suministro 24/7 garantizado',
                'Energía 100% renovable',
                'Precios competitivos',
                'Sin permanencia',
                'Atención al cliente premium',
                'App móvil incluida',
                'Facturación digital',
                'Compensación por excedentes'
            ],
            'distribution' => [
                'Red inteligente avanzada',
                'Monitoreo en tiempo real',
                'Detección automática de fallos',
                'Reparación rápida',
                'Mantenimiento predictivo',
                'Optimización automática',
                'Redundancia de seguridad',
                'Tecnología de última generación'
            ],
            'generation' => [
                'Tecnología de última generación',
                'Máxima eficiencia energética',
                'Mantenimiento mínimo',
                'Garantía extendida',
                'Monitoreo remoto',
                'Integración inteligente',
                'Escalabilidad modular',
                'Certificación internacional'
            ],
            'storage' => [
                'Alta capacidad de almacenamiento',
                'Carga rápida',
                'Vida útil extendida',
                'Control inteligente',
                'Integración con renovables',
                'Backup automático',
                'Gestión de demanda',
                'Tecnología de iones de litio'
            ],
            'consulting' => [
                'Análisis personalizado',
                'Informes detallados',
                'Recomendaciones específicas',
                'Seguimiento continuo',
                'Certificación energética',
                'Optimización de costos',
                'Plan de mejora',
                'Soporte técnico'
            ],
            'maintenance' => [
                'Mantenimiento preventivo',
                'Reparación urgente 24/7',
                'Recambios originales',
                'Técnicos certificados',
                'Garantía de servicio',
                'Historial completo',
                'Mantenimiento predictivo',
                'Soporte remoto'
            ],
            'installation' => [
                'Instalación profesional',
                'Certificación garantizada',
                'Pruebas de funcionamiento',
                'Formación del usuario',
                'Garantía de instalación',
                'Soporte post-instalación',
                'Documentación completa',
                'Cumplimiento normativo'
            ],
            'monitoring' => [
                'Monitoreo 24/7',
                'Alertas automáticas',
                'Dashboard personalizado',
                'Informes detallados',
                'Análisis predictivo',
                'Integración IoT',
                'Notificaciones móviles',
                'Histórico completo'
            ],
            'billing' => [
                'Facturación transparente',
                'Pagos automáticos',
                'Múltiples métodos de pago',
                'Desglose detallado',
                'Historial de consumos',
                'Comparativas mensuales',
                'Alertas de consumo',
                'Soporte financiero'
            ],
            'support' => [
                'Atención 24/7',
                'Técnicos especializados',
                'Resolución rápida',
                'Seguimiento personalizado',
                'Múltiples canales',
                'Escalación automática',
                'SLA garantizado',
                'Satisfacción del cliente'
            ]
        ];

        $features = $featureSets[$serviceType] ?? ['Servicio profesional', 'Calidad garantizada'];
        $maxFeatures = min(6, count($features));
        $minFeatures = min(3, $maxFeatures);
        return $this->faker->randomElements($features, $this->faker->numberBetween($minFeatures, $maxFeatures));
    }

    private function generateRequirements(string $serviceType): array
    {
        $requirementSets = [
            'supply' => [
                'Contador inteligente',
                'Conexión a red eléctrica',
                'Documentación de identidad',
                'Dirección de suministro',
                'Potencia contratada mínima',
                'Certificado de eficiencia energética'
            ],
            'distribution' => [
                'Infraestructura de red existente',
                'Permisos municipales',
                'Estudio de impacto ambiental',
                'Certificación técnica',
                'Plan de seguridad',
                'Personal cualificado'
            ],
            'generation' => [
                'Espacio adecuado para instalación',
                'Orientación solar óptima',
                'Estructura de soporte',
                'Conexión a red',
                'Permisos administrativos',
                'Estudio de viabilidad'
            ],
            'storage' => [
                'Espacio para baterías',
                'Sistema de ventilación',
                'Conexión eléctrica adecuada',
                'Protección contra sobrecargas',
                'Mantenimiento periódico',
                'Certificación de seguridad'
            ],
            'consulting' => [
                'Acceso a instalaciones',
                'Documentación técnica',
                'Historial de consumos',
                'Objetivos de eficiencia',
                'Presupuesto disponible',
                'Compromiso de mejora'
            ],
            'maintenance' => [
                'Acceso a equipos',
                'Documentación técnica',
                'Personal de contacto',
                'Plan de mantenimiento',
                'Recambios disponibles',
                'Herramientas especializadas'
            ],
            'installation' => [
                'Espacio de trabajo',
                'Acceso a instalaciones',
                'Permisos necesarios',
                'Herramientas básicas',
                'Personal de apoyo',
                'Documentación del proyecto'
            ],
            'monitoring' => [
                'Conexión a internet',
                'Dispositivos compatibles',
                'Acceso a datos',
                'Configuración de red',
                'Personal técnico',
                'Sistema de alertas'
            ],
            'billing' => [
                'Datos de consumo',
                'Información de facturación',
                'Métodos de pago',
                'Dirección de envío',
                'Documentación fiscal',
                'Autorizaciones necesarias'
            ],
            'support' => [
                'Información del problema',
                'Acceso remoto',
                'Documentación técnica',
                'Personal de contacto',
                'Sistema de tickets',
                'Escalación de incidencias'
            ]
        ];

        $requirements = $requirementSets[$serviceType] ?? ['Requisitos técnicos básicos'];
        $maxRequirements = min(4, count($requirements));
        $minRequirements = min(2, $maxRequirements);
        return $this->faker->randomElements($requirements, $this->faker->numberBetween($minRequirements, $maxRequirements));
    }

    private function generatePricingDetails(string $pricingModel): array
    {
        $pricingDetails = [
            'fixed' => [
                'Precio fijo mensual',
                'Sin variaciones estacionales',
                'Garantía de precio por 12 meses',
                'Incluye todos los servicios básicos',
                'Sin costes ocultos'
            ],
            'variable' => [
                'Precio según mercado',
                'Actualización mensual',
                'Transparencia total en precios',
                'Posibilidad de ahorro',
                'Seguimiento de tendencias'
            ],
            'tiered' => [
                'Escalones de consumo',
                'Precios decrecientes',
                'Bonificaciones por volumen',
                'Descuentos progresivos',
                'Optimización de costos'
            ],
            'subscription' => [
                'Pago mensual recurrente',
                'Acceso completo al servicio',
                'Actualizaciones incluidas',
                'Soporte prioritario',
                'Cancelación flexible'
            ],
            'pay_per_use' => [
                'Pago solo por uso real',
                'Sin costes fijos',
                'Máxima flexibilidad',
                'Control de gastos',
                'Escalabilidad automática'
            ],
            'contract' => [
                'Precio negociado',
                'Condiciones personalizadas',
                'Estabilidad a largo plazo',
                'Descuentos por volumen',
                'Renovación automática'
            ],
            'free' => [
                'Sin coste inicial',
                'Servicios básicos incluidos',
                'Soporte comunitario',
                'Actualizaciones gratuitas',
                'Uso limitado'
            ],
            'custom' => [
                'Precio personalizado',
                'Negociación individual',
                'Condiciones específicas',
                'Descuentos especiales',
                'Flexibilidad total'
            ]
        ];

        return $pricingDetails[$pricingModel] ?? ['Detalles de precios personalizados'];
    }

    private function generateTermsConditions(): array
    {
        return [
            'Duración mínima del contrato',
            'Condiciones de cancelación',
            'Penalizaciones por incumplimiento',
            'Garantías del servicio',
            'Limitaciones de responsabilidad',
            'Confidencialidad de datos',
            'Modificaciones del contrato',
            'Resolución de conflictos',
            'Cumplimiento normativo',
            'Fuerza mayor'
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'popularity_score' => $this->faker->numberBetween(500, 1000),
        ]);
    }

    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'popularity_score' => $this->faker->numberBetween(200, 800),
        ]);
    }

    public function highDemand(): static
    {
        return $this->state(fn (array $attributes) => [
            'popularity_score' => $this->faker->numberBetween(700, 1000),
        ]);
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }

    public function renewable(): static
    {
        return $this->state(fn (array $attributes) => [
            'energy_source' => $this->faker->randomElement(['solar', 'wind', 'hydro', 'biomass', 'geothermal']),
            'service_type' => $this->faker->randomElement(['generation', 'supply', 'consulting', 'installation']),
        ]);
    }

    public function smartHome(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_type' => 'smart_home',
            'energy_source' => 'electricity',
            'features' => [
                'Control inteligente del hogar',
                'Automatización energética',
                'Monitoreo en tiempo real',
                'Optimización automática',
                'Integración con dispositivos IoT',
                'Ahorro energético garantizado'
            ],
        ]);
    }

    public function electricVehicle(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_type' => 'electric_vehicle',
            'energy_source' => 'electricity',
            'features' => [
                'Carga rápida de vehículos',
                'Puntos de carga inteligentes',
                'Gestión de flotas',
                'Integración con renovables',
                'Monitoreo de carga',
                'Optimización de costos'
            ],
        ]);
    }
}
