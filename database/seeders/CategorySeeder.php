<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen categorías
        if (Category::count() > 0) {
            $this->command->info('Ya existen categorías en la base de datos. Creando categorías adicionales...');
        }

        // Crear categorías para diferentes tipos de contenido
        $this->createNewsCategories();
        $this->createEventCategories();
        $this->createProfessionCategories();
        $this->createCooperativeCategories();
        $this->createEnergyCategories();
        $this->createAdditionalNewsCategories();
        $this->createAdditionalEventCategories();
        $this->createAdditionalProfessionCategories();
        $this->createAdditionalCooperativeCategories();
        $this->createAdditionalEnergyCategories();

        $this->command->info('✅ Se han creado/actualizado las categorías del sistema.');
    }

    private function createNewsCategories(): void
    {
        $categories = [
            [
                'name' => 'Política Energética',
                'description' => 'Noticias sobre políticas y regulaciones energéticas',
                'icon' => 'lightning-bolt',
                'color' => '#DC2626',
                'type' => 'news',
                'sort_order' => 1,
                'is_featured' => true,
            ],
            [
                'name' => 'Innovación Tecnológica',
                'description' => 'Avances tecnológicos en energías renovables',
                'icon' => 'chip',
                'color' => '#2563EB',
                'type' => 'news',
                'sort_order' => 2,
                'is_featured' => true,
            ],
            [
                'name' => 'Mercado Energético',
                'description' => 'Análisis del mercado energético y precios',
                'icon' => 'chart-bar',
                'color' => '#059669',
                'type' => 'news',
                'sort_order' => 3,
                'is_featured' => false,
            ],
            [
                'name' => 'Impacto Ambiental',
                'description' => 'Noticias sobre el impacto ambiental de la energía',
                'icon' => 'leaf',
                'color' => '#16A34A',
                'type' => 'news',
                'sort_order' => 4,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createEventCategories(): void
    {
        $categories = [
            [
                'name' => 'Ferias Energéticas',
                'description' => 'Ferias y exposiciones sobre energía renovable',
                'icon' => 'calendar',
                'color' => '#7C3AED',
                'type' => 'event',
                'sort_order' => 1,
                'is_featured' => true,
            ],
            [
                'name' => 'Conferencias Sostenibles',
                'description' => 'Conferencias sobre sostenibilidad y energía',
                'icon' => 'academic-cap',
                'color' => '#EA580C',
                'type' => 'event',
                'sort_order' => 2,
                'is_featured' => true,
            ],
            [
                'name' => 'Talleres Prácticos',
                'description' => 'Talleres de instalación y mantenimiento',
                'icon' => 'wrench-screwdriver',
                'color' => '#0891B2',
                'type' => 'event',
                'sort_order' => 3,
                'is_featured' => false,
            ],
            [
                'name' => 'Networking Energético',
                'description' => 'Eventos de networking para profesionales del sector',
                'icon' => 'user-group',
                'color' => '#DB2777',
                'type' => 'event',
                'sort_order' => 4,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createProfessionCategories(): void
    {
        $categories = [
            [
                'name' => 'Instaladores Solares',
                'description' => 'Profesionales especializados en instalaciones solares',
                'icon' => 'sun',
                'color' => '#F59E0B',
                'type' => 'profession',
                'sort_order' => 1,
                'is_featured' => true,
            ],
            [
                'name' => 'Ingenieros Energéticos',
                'description' => 'Ingenieros especializados en sistemas energéticos',
                'icon' => 'cog',
                'color' => '#1F2937',
                'type' => 'profession',
                'sort_order' => 2,
                'is_featured' => true,
            ],
            [
                'name' => 'Consultores Sostenibles',
                'description' => 'Consultores en sostenibilidad y eficiencia energética',
                'icon' => 'clipboard-document-list',
                'color' => '#059669',
                'type' => 'profession',
                'sort_order' => 3,
                'is_featured' => false,
            ],
            [
                'name' => 'Auditores Energéticos',
                'description' => 'Profesionales de auditoría energética',
                'icon' => 'magnifying-glass',
                'color' => '#7C2D12',
                'type' => 'profession',
                'sort_order' => 4,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createCooperativeCategories(): void
    {
        $categories = [
            [
                'name' => 'Cooperativas Solares',
                'description' => 'Cooperativas de energía solar comunitaria',
                'icon' => 'building-office-2',
                'color' => '#F59E0B',
                'type' => 'cooperative',
                'sort_order' => 1,
                'is_featured' => true,
            ],
            [
                'name' => 'Cooperativas Eólicas',
                'description' => 'Cooperativas de energía eólica',
                'icon' => 'wind',
                'color' => '#0891B2',
                'type' => 'cooperative',
                'sort_order' => 2,
                'is_featured' => false,
            ],
            [
                'name' => 'Cooperativas de Eficiencia',
                'description' => 'Cooperativas de eficiencia energética',
                'icon' => 'light-bulb',
                'color' => '#DC2626',
                'type' => 'cooperative',
                'sort_order' => 3,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createEnergyCategories(): void
    {
        $categories = [
            [
                'name' => 'Energía Solar',
                'description' => 'Categoría para contenido relacionado con energía solar',
                'icon' => 'sun',
                'color' => '#F59E0B',
                'type' => 'energy',
                'sort_order' => 1,
                'is_featured' => true,
            ],
            [
                'name' => 'Energía Eólica',
                'description' => 'Categoría para contenido relacionado con energía eólica',
                'icon' => 'wind',
                'color' => '#0891B2',
                'type' => 'energy',
                'sort_order' => 2,
                'is_featured' => true,
            ],
            [
                'name' => 'Energía Hidroeléctrica',
                'description' => 'Categoría para contenido relacionado con energía hidroeléctrica',
                'icon' => 'droplet',
                'color' => '#2563EB',
                'type' => 'energy',
                'sort_order' => 3,
                'is_featured' => false,
            ],
            [
                'name' => 'Biomasa',
                'description' => 'Categoría para contenido relacionado con biomasa',
                'icon' => 'fire',
                'color' => '#DC2626',
                'type' => 'energy',
                'sort_order' => 4,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createAdditionalNewsCategories(): void
    {
        $categories = [
            [
                'name' => 'Investigación Energética',
                'description' => 'Investigaciones y estudios sobre energía renovable',
                'icon' => 'beaker',
                'color' => '#7C3AED',
                'type' => 'news',
                'sort_order' => 5,
                'is_featured' => false,
            ],
            [
                'name' => 'Legislación Ambiental',
                'description' => 'Nuevas leyes y regulaciones ambientales',
                'icon' => 'document-text',
                'color' => '#059669',
                'type' => 'news',
                'sort_order' => 6,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createAdditionalEventCategories(): void
    {
        $categories = [
            [
                'name' => 'Webinars Online',
                'description' => 'Webinars sobre temas energéticos',
                'icon' => 'computer-desktop',
                'color' => '#2563EB',
                'type' => 'event',
                'sort_order' => 5,
                'is_featured' => false,
            ],
            [
                'name' => 'Exposiciones Virtuales',
                'description' => 'Exposiciones virtuales de tecnología energética',
                'icon' => 'globe-alt',
                'color' => '#DB2777',
                'type' => 'event',
                'sort_order' => 6,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createAdditionalProfessionCategories(): void
    {
        $categories = [
            [
                'name' => 'Técnicos de Mantenimiento',
                'description' => 'Técnicos especializados en mantenimiento energético',
                'icon' => 'wrench',
                'color' => '#0891B2',
                'type' => 'profession',
                'sort_order' => 5,
                'is_featured' => false,
            ],
            [
                'name' => 'Vendedores Técnicos',
                'description' => 'Vendedores con conocimiento técnico del sector',
                'icon' => 'presentation-chart-line',
                'color' => '#EA580C',
                'type' => 'profession',
                'sort_order' => 6,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createAdditionalCooperativeCategories(): void
    {
        $categories = [
            [
                'name' => 'Cooperativas de Biomasa',
                'description' => 'Cooperativas de energía de biomasa',
                'icon' => 'fire',
                'color' => '#DC2626',
                'type' => 'cooperative',
                'sort_order' => 4,
                'is_featured' => false,
            ],
            [
                'name' => 'Cooperativas de Distribución',
                'description' => 'Cooperativas de distribución energética',
                'icon' => 'truck',
                'color' => '#1F2937',
                'type' => 'cooperative',
                'sort_order' => 5,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createAdditionalEnergyCategories(): void
    {
        $categories = [
            [
                'name' => 'Energía Geotérmica',
                'description' => 'Categoría para contenido relacionado con energía geotérmica',
                'icon' => 'mountain',
                'color' => '#7C2D12',
                'type' => 'energy',
                'sort_order' => 5,
                'is_featured' => false,
            ],
            [
                'name' => 'Energía Marina',
                'description' => 'Categoría para contenido relacionado con energía marina',
                'icon' => 'water',
                'color' => '#2563EB',
                'type' => 'energy',
                'sort_order' => 6,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createProjectCategories(): void
    {
        $categories = [
            [
                'name' => 'Proyectos Solares',
                'description' => 'Proyectos de instalación solar residencial y comercial',
                'icon' => 'home',
                'color' => '#F59E0B',
                'type' => 'project',
                'sort_order' => 1,
                'is_featured' => true,
            ],
            [
                'name' => 'Proyectos Comunitarios',
                'description' => 'Proyectos energéticos de la comunidad',
                'icon' => 'users',
                'color' => '#059669',
                'type' => 'project',
                'sort_order' => 2,
                'is_featured' => true,
            ],
            [
                'name' => 'Proyectos Industriales',
                'description' => 'Proyectos energéticos industriales',
                'icon' => 'building-office',
                'color' => '#1F2937',
                'type' => 'project',
                'sort_order' => 3,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createTechnologyCategories(): void
    {
        $categories = [
            [
                'name' => 'Paneles Solares',
                'description' => 'Tecnología de paneles solares',
                'icon' => 'squares-2x2',
                'color' => '#F59E0B',
                'type' => 'technology',
                'sort_order' => 1,
                'is_featured' => true,
            ],
            [
                'name' => 'Baterías',
                'description' => 'Sistemas de almacenamiento de energía',
                'icon' => 'battery-100',
                'color' => '#059669',
                'type' => 'technology',
                'sort_order' => 2,
                'is_featured' => true,
            ],
            [
                'name' => 'Smart Grid',
                'description' => 'Tecnologías de red inteligente',
                'icon' => 'cpu-chip',
                'color' => '#2563EB',
                'type' => 'technology',
                'sort_order' => 3,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createSustainabilityCategories(): void
    {
        $categories = [
            [
                'name' => 'Eficiencia Energética',
                'description' => 'Mejoras en eficiencia energética',
                'icon' => 'light-bulb',
                'color' => '#16A34A',
                'type' => 'sustainability',
                'sort_order' => 1,
                'is_featured' => true,
            ],
            [
                'name' => 'Reducción de CO2',
                'description' => 'Estrategias para reducir emisiones de CO2',
                'icon' => 'cloud',
                'color' => '#7C3AED',
                'type' => 'sustainability',
                'sort_order' => 2,
                'is_featured' => true,
            ],
            [
                'name' => 'Economía Circular',
                'description' => 'Principios de economía circular en energía',
                'icon' => 'arrow-path',
                'color' => '#EA580C',
                'type' => 'sustainability',
                'sort_order' => 3,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createCommunityCategories(): void
    {
        $categories = [
            [
                'name' => 'Grupos de Usuarios',
                'description' => 'Comunidades de usuarios de energía renovable',
                'icon' => 'user-group',
                'color' => '#DB2777',
                'type' => 'community',
                'sort_order' => 1,
                'is_featured' => true,
            ],
            [
                'name' => 'Foros de Discusión',
                'description' => 'Foros para discutir temas energéticos',
                'icon' => 'chat-bubble-left-right',
                'color' => '#0891B2',
                'type' => 'community',
                'sort_order' => 2,
                'is_featured' => false,
            ],
            [
                'name' => 'Eventos Locales',
                'description' => 'Eventos energéticos locales',
                'icon' => 'map-pin',
                'color' => '#DC2626',
                'type' => 'community',
                'sort_order' => 3,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createMarketplaceCategories(): void
    {
        $categories = [
            [
                'name' => 'Tecnología Usada',
                'description' => 'Tecnología de segunda mano en buen estado',
                'icon' => 'arrow-path',
                'color' => '#6B7280',
                'type' => 'marketplace',
                'sort_order' => 1,
                'is_featured' => false,
            ],
            [
                'name' => 'Servicios Profesionales',
                'description' => 'Servicios de profesionales del sector',
                'icon' => 'wrench-screwdriver',
                'color' => '#059669',
                'type' => 'marketplace',
                'sort_order' => 2,
                'is_featured' => false,
            ],
            [
                'name' => 'Materiales',
                'description' => 'Materiales para instalaciones energéticas',
                'icon' => 'cube',
                'color' => '#7C2D12',
                'type' => 'marketplace',
                'sort_order' => 3,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createOrUpdateCategory($categoryData);
        }
    }

    private function createOrUpdateCategory(array $categoryData): void
    {
        // Generar slug si no existe
        if (!isset($categoryData['slug'])) {
            $categoryData['slug'] = Str::slug($categoryData['name']);
        }

        // Buscar categoría existente por nombre y tipo
        $existingCategory = Category::where('name', $categoryData['name'])
                                  ->where('type', $categoryData['type'])
                                  ->first();

        if ($existingCategory) {
            // Actualizar categoría existente
            $existingCategory->update($categoryData);
            $this->command->info("✅ Categoría actualizada: {$categoryData['name']} ({$categoryData['type']})");
        } else {
            // Crear nueva categoría
            $categoryData['is_active'] = true;
            $categoryData['metadata'] = json_encode([
                'created_by' => 'seeder',
                'seeder_version' => '1.0',
                'created_at' => now()->toISOString(),
            ]);
            
            Category::create($categoryData);
            $this->command->info("🆕 Categoría creada: {$categoryData['name']} ({$categoryData['type']})");
        }
    }
}
