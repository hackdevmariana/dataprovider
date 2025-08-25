<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserBookmark;
use App\Models\User;
use App\Models\Cooperative;
use App\Models\Region;
use App\Models\TopicPost;
use App\Models\EnergyInstallation;
use App\Models\PlantSpecies;
use Carbon\Carbon;

class UserBookmarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar bookmarks existentes
        UserBookmark::truncate();

        $users = User::all();
        $cooperatives = Cooperative::all();
        $regions = Region::all();
        $posts = TopicPost::all();
        $installations = EnergyInstallation::all();
        $species = PlantSpecies::all();

        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles para crear bookmarks.');
            return;
        }

        $folders = [
            'Instalaciones Energéticas',
            'Legislación y Normativas',
            'Proyectos Sostenibles',
            'Especies Vegetales',
            'Posts Interesantes',
            'Cooperativas',
            'Regiones',
            'Investigación',
            'Tutoriales',
            'Noticias Importantes'
        ];

        $tags = [
            'energía-renovable',
            'sostenibilidad',
            'eficiencia-energética',
            'medio-ambiente',
            'tecnología',
            'innovación',
            'comunidad',
            'educación',
            'investigación',
            'desarrollo',
            'política-energética',
            'cambio-climático',
            'biodiversidad',
            'agricultura-sostenible',
            'smart-grid'
        ];

        // Crear bookmarks para cada usuario
        foreach ($users as $user) {
            $this->createBookmarksForUser($user, $cooperatives, $regions, $posts, $installations, $species, $folders, $tags);
        }

        $this->command->info('✅ Se han creado ' . UserBookmark::count() . ' bookmarks de usuario.');
    }

    private function createBookmarksForUser($user, $cooperatives, $regions, $posts, $installations, $species, $folders, $tags): void
    {
        // Crear bookmarks únicos para cada usuario
        $this->createBookmarksForType($user, 'cooperative', $cooperatives, $folders, $tags);
        $this->createBookmarksForType($user, 'region', $regions, $folders, $tags);
        $this->createBookmarksForType($user, 'post', $posts, $folders, $tags);
        $this->createBookmarksForType($user, 'installation', $installations, $folders, $tags);
        $this->createBookmarksForType($user, 'species', $species, $folders, $tags);
    }

    private function createBookmarksForType($user, $contentType, $contentCollection, $folders, $tags): void
    {
        if ($contentCollection->isEmpty()) {
            return;
        }

        // Crear 1-3 bookmarks por tipo de contenido
        $count = min(rand(1, 3), $contentCollection->count());
        $selectedContent = $contentCollection->random($count);
        
        foreach ($selectedContent as $content) {
            $bookmarkData = $this->generateBookmarkData($user, $contentType, $content, $folders, $tags);
            UserBookmark::create($bookmarkData);
        }
    }

    private function generateBookmarkData($user, $contentType, $content, $folders, $tags): array
    {
        $folder = $this->getFolderForContentType($contentType, $folders);
        $selectedTags = $this->getRandomTags($tags, rand(2, 5));
        $priority = $this->getRandomPriority();
        $reminderEnabled = rand(1, 10) <= 3; // 30% de probabilidad de tener recordatorio
        
        return [
            'user_id' => $user->id,
            'bookmarkable_type' => $this->getBookmarkableType($contentType),
            'bookmarkable_id' => $content->id,
            'folder' => $folder,
            'tags' => $selectedTags,
            'personal_notes' => $this->getPersonalNotes($contentType, $content),
            'priority' => $priority,
            'reminder_enabled' => $reminderEnabled,
            'reminder_date' => $reminderEnabled ? $this->getRandomReminderDate() : null,
            'reminder_frequency' => $reminderEnabled ? $this->getRandomReminderFrequency() : null,
            'access_count' => rand(0, 25),
            'last_accessed_at' => $this->getRandomLastAccessedDate(),
            'is_public' => rand(1, 10) <= 4, // 40% de probabilidad de ser público
        ];
    }

    private function getFolderForContentType(string $contentType, array $folders): string
    {
        return match ($contentType) {
            'cooperative' => $folders[array_rand(['Cooperativas', 'Proyectos Sostenibles', 'Comunidad'])],
            'region' => $folders[array_rand(['Regiones', 'Legislación y Normativas', 'Investigación'])],
            'post' => $folders[array_rand(['Posts Interesantes', 'Educación', 'Noticias Importantes'])],
            'installation' => $folders[array_rand(['Instalaciones Energéticas', 'Tecnología', 'Innovación'])],
            'species' => $folders[array_rand(['Especies Vegetales', 'Biodiversidad', 'Agricultura Sostenible'])],
            default => $folders[array_rand($folders)],
        };
    }

    private function getBookmarkableType(string $contentType): string
    {
        return match ($contentType) {
            'cooperative' => 'App\Models\Cooperative',
            'region' => 'App\Models\Region',
            'post' => 'App\Models\TopicPost',
            'installation' => 'App\Models\EnergyInstallation',
            'species' => 'App\Models\PlantSpecies',
            default => 'App\Models\Cooperative',
        };
    }

    private function getRandomTags(array $tags, int $count): array
    {
        $selectedTags = array_rand($tags, min($count, count($tags)));
        
        if (!is_array($selectedTags)) {
            $selectedTags = [$selectedTags];
        }
        
        return array_map(fn($index) => $tags[$index], $selectedTags);
    }

    private function getRandomPriority(): int
    {
        $weights = [70, 20, 10]; // 70% normal, 20% importante, 10% urgente
        $random = rand(1, 100);
        
        if ($random <= $weights[0]) return 0; // normal
        if ($random <= $weights[0] + $weights[1]) return 1; // importante
        return 2; // urgente
    }

    private function getPersonalNotes(string $contentType, $content): ?string
    {
        if (rand(1, 10) <= 6) { // 60% de probabilidad de tener notas
            return match ($contentType) {
                'cooperative' => $this->getCooperativeNotes($content),
                'region' => $this->getRegionNotes($content),
                'post' => $this->getPostNotes($content),
                'installation' => $this->getInstallationNotes($content),
                'species' => $this->getSpeciesNotes($content),
                default => $this->getGenericNotes($content),
            };
        }
        
        return null;
    }

    private function getCooperativeNotes($cooperative): string
    {
        $notes = [
            "Interesante modelo de gestión cooperativa. Revisar para implementar en mi región.",
            "Excelente ejemplo de sostenibilidad energética. Tomar notas para proyecto futuro.",
            "Cooperativa con buenas prácticas ambientales. Contactar para colaboración.",
            "Modelo de negocio innovador. Estudiar para replicar en mi área.",
            "Buena gestión de recursos renovables. Aplicar conceptos en mi proyecto.",
        ];
        
        return $notes[array_rand($notes)];
    }

    private function getRegionNotes($region): string
    {
        $notes = [
            "Región con políticas ambientales avanzadas. Investigar normativas aplicables.",
            "Zona con potencial para energías renovables. Considerar para inversión.",
            "Área con buenas prácticas de sostenibilidad. Aplicar en mi comunidad.",
            "Región líder en transición energética. Seguir como referencia.",
            "Zona con incentivos para proyectos verdes. Evaluar oportunidades.",
        ];
        
        return $notes[array_rand($notes)];
    }

    private function getPostNotes($post): string
    {
        $notes = [
            "Post muy informativo sobre sostenibilidad. Revisar conceptos clave.",
            "Excelente explicación técnica. Guardar para referencia futura.",
            "Información útil para mi proyecto. Consultar cuando sea necesario.",
            "Buen recurso educativo. Compartir con el equipo.",
            "Contenido relevante para mi investigación. Revisar en detalle.",
        ];
        
        return $notes[array_rand($notes)];
    }

    private function getInstallationNotes($installation): string
    {
        $notes = [
            "Instalación con tecnología avanzada. Estudiar para mi proyecto.",
            "Excelente ejemplo de eficiencia energética. Aplicar conceptos.",
            "Sistema bien diseñado. Considerar para implementación futura.",
            "Buena integración de energías renovables. Replicar en mi instalación.",
            "Instalación con buenas prácticas. Seguir como modelo.",
        ];
        
        return $notes[array_rand($notes)];
    }

    private function getSpeciesNotes($species): string
    {
        $notes = [
            "Especie ideal para mi zona climática. Considerar para plantación.",
            "Excelente para captura de CO2. Incluir en proyecto de reforestación.",
            "Especie resistente y sostenible. Perfecta para mi jardín.",
            "Buena opción para biodiversidad local. Promover en la comunidad.",
            "Especie con múltiples beneficios ambientales. Investigar más.",
        ];
        
        return $notes[array_rand($notes)];
    }

    private function getGenericNotes($content): string
    {
        $notes = [
            "Contenido interesante. Revisar más adelante.",
            "Información útil para mi proyecto. Consultar cuando sea necesario.",
            "Recurso valioso. Guardar para referencia futura.",
            "Contenido relevante. Estudiar en detalle.",
            "Información importante. Aplicar en mi trabajo.",
        ];
        
        return $notes[array_rand($notes)];
    }

    private function getRandomReminderDate(): Carbon
    {
        return now()->addDays(rand(1, 90));
    }

    private function getRandomReminderFrequency(): string
    {
        $frequencies = ['once', 'weekly', 'monthly'];
        return $frequencies[array_rand($frequencies)];
    }

    private function getRandomLastAccessedDate(): ?Carbon
    {
        if (rand(1, 10) <= 7) { // 70% de probabilidad de haber sido accedido
            return now()->subDays(rand(0, 30));
        }
        
        return null;
    }
}
