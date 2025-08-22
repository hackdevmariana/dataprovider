<?php

namespace Database\Seeders;

use App\Models\RelationshipType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RelationshipTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tipos de relaciones familiares y personales
        $relationshipTypes = [
            // RELACIONES FAMILIARES (category: family)
            [
                'name' => 'Padre',
                'slug' => 'padre',
                'reciprocal_slug' => 'hijo',
                'category' => 'family',
                'degree' => 1,
                'gender_specific' => true,
                'description' => 'Relación padre-hijo, primer grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Hijo',
                'slug' => 'hijo',
                'reciprocal_slug' => 'padre',
                'category' => 'family',
                'degree' => 1,
                'gender_specific' => true,
                'description' => 'Relación hijo-padre, primer grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Madre',
                'slug' => 'madre',
                'reciprocal_slug' => 'hijo',
                'category' => 'family',
                'degree' => 1,
                'gender_specific' => true,
                'description' => 'Relación madre-hijo, primer grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Hija',
                'slug' => 'hija',
                'reciprocal_slug' => 'padre',
                'category' => 'family',
                'degree' => 1,
                'gender_specific' => true,
                'description' => 'Relación hija-padre, primer grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Hermano',
                'slug' => 'hermano',
                'reciprocal_slug' => 'hermano',
                'category' => 'family',
                'degree' => 2,
                'gender_specific' => true,
                'description' => 'Relación entre hermanos, segundo grado de consanguinidad',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Hermana',
                'slug' => 'hermana',
                'reciprocal_slug' => 'hermana',
                'category' => 'family',
                'degree' => 2,
                'gender_specific' => true,
                'description' => 'Relación entre hermanas, segundo grado de consanguinidad',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Abuelo',
                'slug' => 'abuelo',
                'reciprocal_slug' => 'nieto',
                'category' => 'family',
                'degree' => 2,
                'gender_specific' => true,
                'description' => 'Relación abuelo-nieto, segundo grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Abuela',
                'slug' => 'abuela',
                'reciprocal_slug' => 'nieto',
                'category' => 'family',
                'degree' => 2,
                'gender_specific' => true,
                'description' => 'Relación abuela-nieto, segundo grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Nieto',
                'slug' => 'nieto',
                'reciprocal_slug' => 'abuelo',
                'category' => 'family',
                'degree' => 2,
                'gender_specific' => true,
                'description' => 'Relación nieto-abuelo, segundo grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Nieta',
                'slug' => 'nieta',
                'reciprocal_slug' => 'abuelo',
                'category' => 'family',
                'degree' => 2,
                'gender_specific' => true,
                'description' => 'Relación nieta-abuelo, segundo grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Tío',
                'slug' => 'tio',
                'reciprocal_slug' => 'sobrino',
                'category' => 'family',
                'degree' => 3,
                'gender_specific' => true,
                'description' => 'Relación tío-sobrino, tercer grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Tía',
                'slug' => 'tia',
                'reciprocal_slug' => 'sobrino',
                'category' => 'family',
                'degree' => 3,
                'gender_specific' => true,
                'description' => 'Relación tía-sobrino, tercer grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Sobrino',
                'slug' => 'sobrino',
                'reciprocal_slug' => 'tio',
                'category' => 'family',
                'degree' => 3,
                'gender_specific' => true,
                'description' => 'Relación sobrino-tío, tercer grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Sobrina',
                'slug' => 'sobrina',
                'reciprocal_slug' => 'tio',
                'category' => 'family',
                'degree' => 3,
                'gender_specific' => true,
                'description' => 'Relación sobrina-tío, tercer grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Primo',
                'slug' => 'primo',
                'reciprocal_slug' => 'primo',
                'category' => 'family',
                'degree' => 4,
                'gender_specific' => true,
                'description' => 'Relación entre primos, cuarto grado de consanguinidad',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Prima',
                'slug' => 'prima',
                'reciprocal_slug' => 'prima',
                'category' => 'family',
                'degree' => 4,
                'gender_specific' => true,
                'description' => 'Relación entre primas, cuarto grado de consanguinidad',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Bisabuelo',
                'slug' => 'bisabuelo',
                'reciprocal_slug' => 'bisnieto',
                'category' => 'family',
                'degree' => 3,
                'gender_specific' => true,
                'description' => 'Relación bisabuelo-bisnieto, tercer grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Bisabuela',
                'slug' => 'bisabuela',
                'reciprocal_slug' => 'bisnieto',
                'category' => 'family',
                'degree' => 3,
                'gender_specific' => true,
                'description' => 'Relación bisabuela-bisnieto, tercer grado de consanguinidad',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Cuñado',
                'slug' => 'cunado',
                'reciprocal_slug' => 'cunado',
                'category' => 'family',
                'degree' => null,
                'gender_specific' => true,
                'description' => 'Relación por afinidad, hermano del cónyuge',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Cuñada',
                'slug' => 'cunada',
                'reciprocal_slug' => 'cunada',
                'category' => 'family',
                'degree' => null,
                'gender_specific' => true,
                'description' => 'Relación por afinidad, hermana del cónyuge',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Suegro',
                'slug' => 'suegro',
                'reciprocal_slug' => 'yerno',
                'category' => 'family',
                'degree' => null,
                'gender_specific' => true,
                'description' => 'Relación por afinidad, padre del cónyuge',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Suegra',
                'slug' => 'suegra',
                'reciprocal_slug' => 'nuera',
                'category' => 'family',
                'degree' => null,
                'gender_specific' => true,
                'description' => 'Relación por afinidad, madre del cónyuge',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Yerno',
                'slug' => 'yerno',
                'reciprocal_slug' => 'suegro',
                'category' => 'family',
                'degree' => null,
                'gender_specific' => true,
                'description' => 'Relación por afinidad, esposo de la hija',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Nuera',
                'slug' => 'nuera',
                'reciprocal_slug' => 'suegra',
                'category' => 'family',
                'degree' => null,
                'gender_specific' => true,
                'description' => 'Relación por afinidad, esposa del hijo',
                'is_symmetrical' => false,
            ],

            // RELACIONES LEGALES (category: legal)
            [
                'name' => 'Cónyuge',
                'slug' => 'conyuge',
                'reciprocal_slug' => 'conyuge',
                'category' => 'legal',
                'degree' => null,
                'gender_specific' => false,
                'description' => 'Relación legal por matrimonio o unión civil',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Ex Cónyuge',
                'slug' => 'ex-conyuge',
                'reciprocal_slug' => 'ex-conyuge',
                'category' => 'legal',
                'degree' => null,
                'gender_specific' => false,
                'description' => 'Relación legal anterior por matrimonio disuelto',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Pareja de Hecho',
                'slug' => 'pareja-hecho',
                'reciprocal_slug' => 'pareja-hecho',
                'category' => 'legal',
                'degree' => null,
                'gender_specific' => false,
                'description' => 'Relación de convivencia estable sin matrimonio',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Tutor Legal',
                'slug' => 'tutor-legal',
                'reciprocal_slug' => 'tutelado',
                'category' => 'legal',
                'degree' => null,
                'gender_specific' => false,
                'description' => 'Relación legal de tutela y representación',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Tutelado',
                'slug' => 'tutelado',
                'reciprocal_slug' => 'tutor-legal',
                'category' => 'legal',
                'degree' => null,
                'gender_specific' => false,
                'description' => 'Persona bajo tutela legal',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Adoptado',
                'slug' => 'adoptado',
                'reciprocal_slug' => 'padre-adoptivo',
                'category' => 'legal',
                'degree' => 1,
                'gender_specific' => false,
                'description' => 'Relación legal por adopción',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Padre Adoptivo',
                'slug' => 'padre-adoptivo',
                'reciprocal_slug' => 'adoptado',
                'category' => 'legal',
                'degree' => 1,
                'gender_specific' => true,
                'description' => 'Relación legal por adopción',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Madre Adoptiva',
                'slug' => 'madre-adoptiva',
                'reciprocal_slug' => 'adoptado',
                'category' => 'legal',
                'degree' => 1,
                'gender_specific' => true,
                'description' => 'Relación legal por adopción',
                'is_symmetrical' => false,
            ],

            // RELACIONES SENTIMENTALES (category: sentimental)
            [
                'name' => 'Novio',
                'slug' => 'novio',
                'reciprocal_slug' => 'novia',
                'category' => 'sentimental',
                'degree' => null,
                'gender_specific' => true,
                'description' => 'Relación sentimental sin compromiso legal',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Novia',
                'slug' => 'novia',
                'reciprocal_slug' => 'novio',
                'category' => 'sentimental',
                'degree' => null,
                'gender_specific' => true,
                'description' => 'Relación sentimental sin compromiso legal',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Ex Novio',
                'slug' => 'ex-novio',
                'reciprocal_slug' => 'ex-novia',
                'category' => 'sentimental',
                'degree' => null,
                'gender_specific' => true,
                'description' => 'Relación sentimental anterior',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Ex Novia',
                'slug' => 'ex-novia',
                'reciprocal_slug' => 'ex-novio',
                'category' => 'sentimental',
                'degree' => null,
                'gender_specific' => true,
                'description' => 'Relación sentimental anterior',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Amigo',
                'slug' => 'amigo',
                'reciprocal_slug' => 'amigo',
                'category' => 'sentimental',
                'degree' => null,
                'gender_specific' => false,
                'description' => 'Relación de amistad cercana',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Amiga',
                'slug' => 'amiga',
                'reciprocal_slug' => 'amiga',
                'category' => 'sentimental',
                'degree' => null,
                'gender_specific' => true,
                'description' => 'Relación de amistad cercana',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Mejor Amigo',
                'slug' => 'mejor-amigo',
                'reciprocal_slug' => 'mejor-amigo',
                'category' => 'sentimental',
                'degree' => null,
                'gender_specific' => false,
                'description' => 'Relación de amistad muy cercana',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Mejor Amiga',
                'slug' => 'mejor-amiga',
                'reciprocal_slug' => 'mejor-amiga',
                'category' => 'sentimental',
                'degree' => null,
                'gender_specific' => true,
                'description' => 'Relación de amistad muy cercana',
                'is_symmetrical' => true,
            ],

            // OTROS TIPOS (category: otro)
            [
                'name' => 'Colega',
                'slug' => 'colega',
                'reciprocal_slug' => 'colega',
                'category' => 'otro',
                'degree' => null,
                'gender_specific' => false,
                'description' => 'Relación profesional o laboral',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Mentor',
                'slug' => 'mentor',
                'reciprocal_slug' => 'mentorado',
                'category' => 'otro',
                'degree' => null,
                'gender_specific' => false,
                'description' => 'Relación de guía y enseñanza',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Mentorado',
                'slug' => 'mentorado',
                'reciprocal_slug' => 'mentor',
                'category' => 'otro',
                'degree' => null,
                'gender_specific' => false,
                'description' => 'Persona que recibe guía y enseñanza',
                'is_symmetrical' => false,
            ],
            [
                'name' => 'Vecino',
                'slug' => 'vecino',
                'reciprocal_slug' => 'vecino',
                'category' => 'otro',
                'degree' => null,
                'gender_specific' => false,
                'description' => 'Relación por proximidad geográfica',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Compañero de Clase',
                'slug' => 'companero-clase',
                'reciprocal_slug' => 'companero-clase',
                'category' => 'otro',
                'degree' => null,
                'gender_specific' => false,
                'description' => 'Relación educativa compartida',
                'is_symmetrical' => true,
            ],
            [
                'name' => 'Compañero de Trabajo',
                'slug' => 'companero-trabajo',
                'reciprocal_slug' => 'companero-trabajo',
                'category' => 'otro',
                'degree' => null,
                'gender_specific' => false,
                'description' => 'Relación laboral compartida',
                'is_symmetrical' => true,
            ],
        ];

        $createdTypes = [];
        $typeCount = 0;

        foreach ($relationshipTypes as $typeData) {
            $relationshipType = RelationshipType::firstOrCreate(
                ['slug' => $typeData['slug']],
                $typeData
            );
            
            $createdTypes[] = [
                'id' => $relationshipType->id,
                'name' => $relationshipType->name,
                'slug' => $relationshipType->slug,
                'category' => ucfirst($relationshipType->category),
                'degree' => $relationshipType->degree ? "Grado {$relationshipType->degree}" : 'Sin grado',
                'gender_specific' => $relationshipType->gender_specific ? 'Sí' : 'No',
                'symmetrical' => $relationshipType->is_symmetrical ? 'Sí' : 'No',
                'reciprocal' => $relationshipType->reciprocal_slug ?: 'Sin recíproco',
            ];
            
            $typeCount++;
        }

        $this->command->info("Se han creado {$typeCount} tipos de relaciones.");
        
        // Mostrar tabla con los tipos creados
        $displayData = array_slice($createdTypes, 0, 25); // Mostrar solo los primeros 25
        $this->command->table(
            ['ID', 'Nombre', 'Slug', 'Categoría', 'Grado', 'Específico Género', 'Simétrico', 'Recíproco'],
            $displayData
        );
        
        if (count($createdTypes) > 25) {
            $this->command->info("... y " . (count($createdTypes) - 25) . " tipos más.");
        }

        // Estadísticas por categoría
        $categoryStats = RelationshipType::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
        
        // Estadísticas por grado
        $degreeStats = RelationshipType::selectRaw('degree, COUNT(*) as count')
            ->whereNotNull('degree')
            ->groupBy('degree')
            ->pluck('count', 'degree')
            ->toArray();
        
        // Estadísticas por características
        $genderSpecificCount = RelationshipType::where('gender_specific', true)->count();
        $symmetricalCount = RelationshipType::where('is_symmetrical', true)->count();
        $withReciprocalCount = RelationshipType::whereNotNull('reciprocal_slug')->count();
        
        $this->command->newLine();
        $this->command->info("📊 Estadísticas:");
        $this->command->info("   • Total de tipos: {$typeCount}");
        $this->command->info("   • Con recíproco: {$withReciprocalCount}");
        $this->command->info("   • Específicos de género: {$genderSpecificCount}");
        $this->command->info("   • Simétricos: {$symmetricalCount}");
        
        $this->command->newLine();
        $this->command->info("🏷️ Por categoría:");
        foreach ($categoryStats as $category => $count) {
            $categoryLabel = match($category) {
                'family' => 'Familiar',
                'legal' => 'Legal',
                'sentimental' => 'Sentimental',
                'otro' => 'Otros',
                default => ucfirst($category)
            };
            $this->command->info("   • {$categoryLabel}: {$count}");
        }
        
        $this->command->newLine();
        $this->command->info("📏 Por grado de consanguinidad:");
        foreach ($degreeStats as $degree => $count) {
            $this->command->info("   • Grado {$degree}: {$count} tipos");
        }
        
        $this->command->newLine();
        $this->command->info("✅ Seeder de RelationshipType completado exitosamente.");
    }
}
