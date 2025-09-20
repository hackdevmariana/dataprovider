<?php

namespace Database\Seeders;

use App\Models\TagGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear grupos de etiquetas específicos y útiles
        $tagGroups = [
            [
                'name' => 'Categorías de Contenido',
                'slug' => 'categorias-contenido',
                'description' => 'Grupo de etiquetas para categorizar diferentes tipos de contenido como artículos, videos, imágenes, documentos, etc.',
            ],
            [
                'name' => 'Etiquetas de Ubicación',
                'slug' => 'etiquetas-ubicacion',
                'description' => 'Etiquetas para identificar ubicaciones geográficas, ciudades, regiones, países y áreas específicas.',
            ],
            [
                'name' => 'Tipos de Eventos',
                'slug' => 'tipos-eventos',
                'description' => 'Clasificación de diferentes tipos de eventos como conferencias, talleres, conciertos, festivales, etc.',
            ],
            [
                'name' => 'Clasificación por Edad',
                'slug' => 'clasificacion-edad',
                'description' => 'Etiquetas para clasificar contenido según la edad recomendada del público objetivo (infantil, juvenil, adulto, etc.).',
            ],
            [
                'name' => 'Géneros Musicales',
                'slug' => 'generos-musicales',
                'description' => 'Clasificación de diferentes géneros y estilos musicales como rock, pop, clásica, jazz, etc.',
            ],
            [
                'name' => 'Estilos Artísticos',
                'slug' => 'estilos-artisticos',
                'description' => 'Etiquetas para clasificar diferentes estilos y movimientos artísticos.',
            ],
            [
                'name' => 'Niveles de Dificultad',
                'slug' => 'niveles-dificultad',
                'description' => 'Clasificación de contenido según su nivel de dificultad (básico, intermedio, avanzado, experto).',
            ],
            [
                'name' => 'Idiomas Disponibles',
                'slug' => 'idiomas-disponibles',
                'description' => 'Etiquetas para identificar los idiomas en los que está disponible el contenido.',
            ],
            [
                'name' => 'Formatos de Presentación',
                'slug' => 'formatos-presentacion',
                'description' => 'Clasificación según el formato de presentación (presencial, virtual, híbrido, grabado, etc.).',
            ],
            [
                'name' => 'Temas de Interés',
                'slug' => 'temas-interes',
                'description' => 'Etiquetas para categorizar contenido según temas de interés específicos.',
            ],
            [
                'name' => 'Estados de Proyecto',
                'slug' => 'estados-proyecto',
                'description' => 'Clasificación de proyectos según su estado (planificación, en desarrollo, completado, cancelado, etc.).',
            ],
            [
                'name' => 'Prioridades',
                'slug' => 'prioridades',
                'description' => 'Etiquetas para clasificar tareas y elementos según su nivel de prioridad.',
            ],
            [
                'name' => 'Departamentos',
                'slug' => 'departamentos',
                'description' => 'Clasificación de contenido y recursos según el departamento responsable.',
            ],
            [
                'name' => 'Funciones del Personal',
                'slug' => 'funciones-personal',
                'description' => 'Etiquetas para clasificar el personal según sus funciones y responsabilidades.',
            ],
            [
                'name' => 'Tipos de Documentos',
                'slug' => 'tipos-documentos',
                'description' => 'Clasificación de documentos según su tipo (manuales, políticas, procedimientos, etc.).',
            ],
            [
                'name' => 'Clasificación Legal',
                'slug' => 'clasificacion-legal',
                'description' => 'Etiquetas para clasificar contenido según aspectos legales y regulatorios.',
            ],
            [
                'name' => 'Estados de Aprobación',
                'slug' => 'estados-aprobacion',
                'description' => 'Clasificación de contenido según su estado de aprobación (pendiente, aprobado, rechazado, etc.).',
            ],
            [
                'name' => 'Niveles de Acceso',
                'slug' => 'niveles-acceso',
                'description' => 'Etiquetas para clasificar contenido según los niveles de acceso requeridos.',
            ],
            [
                'name' => 'Tipos de Recurso',
                'slug' => 'tipos-recurso',
                'description' => 'Clasificación de recursos según su tipo (humanos, materiales, tecnológicos, etc.).',
            ],
            [
                'name' => 'Categorías de Noticias',
                'slug' => 'categorias-noticias',
                'description' => 'Etiquetas para categorizar noticias y artículos informativos.',
            ],
        ];

        // Insertar los grupos de etiquetas
        foreach ($tagGroups as $tagGroup) {
            TagGroup::updateOrCreate(
                ['slug' => $tagGroup['slug']], // Buscar por slug único
                $tagGroup
            );
        }

        // Crear algunos grupos adicionales únicos
        $additionalGroups = [
            [
                'name' => 'Etiquetas de Temporada',
                'slug' => 'etiquetas-temporada',
                'description' => 'Etiquetas para clasificar contenido según las estaciones del año y eventos estacionales.',
            ],
            [
                'name' => 'Clasificación por Audiencia',
                'slug' => 'clasificacion-audiencia',
                'description' => 'Etiquetas para categorizar contenido según el tipo de audiencia objetivo.',
            ],
            [
                'name' => 'Estados de Proceso',
                'slug' => 'estados-proceso',
                'description' => 'Etiquetas para identificar diferentes estados en procesos de trabajo.',
            ],
            [
                'name' => 'Tipos de Recurso',
                'slug' => 'tipos-recurso',
                'description' => 'Clasificación de diferentes tipos de recursos disponibles.',
            ],
            [
                'name' => 'Categorías de Calidad',
                'slug' => 'categorias-calidad',
                'description' => 'Etiquetas para clasificar la calidad del contenido o servicio.',
            ],
        ];

        foreach ($additionalGroups as $group) {
            TagGroup::updateOrCreate(
                ['slug' => $group['slug']],
                $group
            );
        }
    }
}