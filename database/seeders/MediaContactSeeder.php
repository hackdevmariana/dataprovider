<?php

namespace Database\Seeders;

use App\Models\MediaContact;
use App\Models\MediaOutlet;
use Illuminate\Database\Seeder;

/**
 * Seeder para contactos de medios con datos realistas.
 */
class MediaContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear contactos específicos para medios principales
        $this->createMainMediaContacts();
        
        // Crear contactos adicionales usando factory (temporalmente comentado)
        // MediaContact::factory(25)->create();
        
        // Crear freelancers especializados
        // MediaContact::factory(8)->freelancer()->create();
        
        // Crear contactos de alta prioridad
        // MediaContact::factory(6)->highPriority()->create();
        
        // Crear contactos especializados en sostenibilidad
        // MediaContact::factory(10)->sustainabilityFocused()->create();
        
        echo "✅ Creados " . MediaContact::count() . " contactos de medios\n";
    }

    /**
     * Crear contactos específicos para medios principales.
     */
    private function createMainMediaContacts(): void
    {
        $elPais = MediaOutlet::where('slug', 'el-pais')->first();
        $elMundo = MediaOutlet::where('slug', 'el-mundo')->first();
        $elDiario = MediaOutlet::where('slug', 'eldiario-es')->first();
        $energiasRenovables = MediaOutlet::where('slug', 'energias-renovables')->first();
        $ecoticias = MediaOutlet::where('slug', 'ecoticias')->first();

        $specificContacts = [
            // El País
            [
                'media_outlet_id' => $elPais?->id,
                'contact_name' => 'Pepa Bueno',
                'job_title' => 'Directora',
                'department' => 'Dirección',
                'type' => 'editorial',
                'phone' => '+34 91 337 8200',
                'email' => 'pepa.bueno@elpais.es',
                'specializations' => json_encode(['dirección editorial', 'estrategia', 'política']),
                'coverage_areas' => json_encode(['editorial', 'política nacional', 'estrategia mediática']),
                'preferred_contact_method' => 'email',
                'accepts_press_releases' => false,
                'accepts_interviews' => true,
                'accepts_events_invitations' => true,
                'is_freelancer' => false,
                'is_active' => true,
                'is_verified' => true,
                'priority_level' => 4,
                'response_rate' => 0.95,
                'bio' => 'Directora de El País desde 2018. Anteriormente directora de Hoy por Hoy en la Cadena SER. Licenciada en Ciencias de la Información.',
                'verified_at' => now()->subYear(),
            ],
            [
                'media_outlet_id' => $elPais?->id,
                'contact_name' => 'Manuel Planelles',
                'job_title' => 'Redactor de Medio Ambiente',
                'department' => 'Sociedad',
                'type' => 'editorial',
                'email' => 'manuel.planelles@elpais.es',
                'mobile_phone' => '+34 600 123 456',
                'specializations' => json_encode(['cambio climático', 'políticas ambientales', 'energías renovables']),
                'coverage_areas' => json_encode(['medio ambiente', 'sostenibilidad', 'política climática']),
                'preferred_contact_method' => 'email',
                'accepts_press_releases' => true,
                'accepts_interviews' => true,
                'accepts_events_invitations' => true,
                'is_freelancer' => false,
                'is_active' => true,
                'is_verified' => true,
                'priority_level' => 3,
                'response_rate' => 0.88,
                'bio' => 'Periodista especializado en medio ambiente y cambio climático. Corresponsal en las principales cumbres climáticas internacionales.',
                'verified_at' => now()->subMonths(6),
            ],
            
            // El Mundo
            [
                'media_outlet_id' => $elMundo?->id,
                'contact_name' => 'Francisco Rosell',
                'job_title' => 'Director',
                'department' => 'Dirección',
                'type' => 'editorial',
                'email' => 'francisco.rosell@elmundo.es',
                'phone' => '+34 91 586 4800',
                'specializations' => json_encode(['dirección editorial', 'investigación', 'análisis político']),
                'coverage_areas' => json_encode(['editorial', 'investigación', 'política']),
                'preferred_contact_method' => 'phone',
                'accepts_press_releases' => false,
                'accepts_interviews' => true,
                'accepts_events_invitations' => true,
                'is_freelancer' => false,
                'is_active' => true,
                'is_verified' => true,
                'priority_level' => 4,
                'response_rate' => 0.92,
                'bio' => 'Director de El Mundo. Amplia experiencia en periodismo de investigación y análisis político.',
                'verified_at' => now()->subMonths(8),
            ],
            
            // elDiario.es
            [
                'media_outlet_id' => $elDiario?->id,
                'contact_name' => 'Ignacio Escolar',
                'job_title' => 'Director',
                'department' => 'Dirección',
                'type' => 'editorial',
                'email' => 'ignacio.escolar@eldiario.es',
                'specializations' => json_encode(['periodismo digital', 'innovación mediática', 'sostenibilidad económica']),
                'coverage_areas' => json_encode(['medios digitales', 'innovación', 'derechos sociales']),
                'preferred_contact_method' => 'email',
                'accepts_press_releases' => true,
                'accepts_interviews' => true,
                'accepts_events_invitations' => true,
                'is_freelancer' => false,
                'is_active' => true,
                'is_verified' => true,
                'priority_level' => 4,
                'response_rate' => 0.89,
                'bio' => 'Director y fundador de elDiario.es. Pionero en el modelo de financiación por suscriptores en España.',
                'verified_at' => now()->subMonths(4),
            ],
            [
                'media_outlet_id' => $elDiario?->id,
                'contact_name' => 'Ana Ordaz',
                'job_title' => 'Redactora de Medio Ambiente',
                'department' => 'Sociedad',
                'type' => 'editorial',
                'email' => 'ana.ordaz@eldiario.es',
                'mobile_phone' => '+34 600 789 012',
                'specializations' => json_encode(['biodiversidad', 'conservación', 'políticas ambientales']),
                'coverage_areas' => json_encode(['espacios naturales', 'especies protegidas', 'conservación']),
                'preferred_contact_method' => 'whatsapp',
                'accepts_press_releases' => true,
                'accepts_interviews' => true,
                'accepts_events_invitations' => true,
                'is_freelancer' => false,
                'is_active' => true,
                'is_verified' => true,
                'priority_level' => 3,
                'response_rate' => 0.85,
                'bio' => 'Periodista especializada en biodiversidad y conservación. Licenciada en Biología y Máster en Periodismo Científico.',
                'verified_at' => now()->subMonths(3),
            ],
            
            // Energías Renovables
            [
                'media_outlet_id' => $energiasRenovables?->id,
                'contact_name' => 'Luis Merino',
                'job_title' => 'Director',
                'department' => 'Dirección',
                'type' => 'editorial',
                'email' => 'luis.merino@energias-renovables.com',
                'phone' => '+34 91 234 5678',
                'specializations' => json_encode(['energías renovables', 'sector eléctrico', 'tecnología energética']),
                'coverage_areas' => json_encode(['solar', 'eólica', 'hidráulica', 'política energética']),
                'preferred_contact_method' => 'email',
                'accepts_press_releases' => true,
                'accepts_interviews' => true,
                'accepts_events_invitations' => true,
                'is_freelancer' => false,
                'is_active' => true,
                'is_verified' => true,
                'priority_level' => 3,
                'response_rate' => 0.93,
                'bio' => 'Director de Energías Renovables con más de 20 años de experiencia en el sector energético. Ingeniero Industrial.',
                'verified_at' => now()->subMonths(2),
            ],
            [
                'media_outlet_id' => $energiasRenovables?->id,
                'contact_name' => 'Carmen Becerril',
                'job_title' => 'Redactora Jefe',
                'department' => 'Redacción',
                'type' => 'editorial',
                'email' => 'carmen.becerril@energias-renovables.com',
                'specializations' => json_encode(['eficiencia energética', 'autoconsumo', 'almacenamiento']),
                'coverage_areas' => json_encode(['eficiencia energética', 'smart grids', 'innovación']),
                'preferred_contact_method' => 'email',
                'accepts_press_releases' => true,
                'accepts_interviews' => true,
                'accepts_events_invitations' => true,
                'is_freelancer' => false,
                'is_active' => true,
                'is_verified' => true,
                'priority_level' => 3,
                'response_rate' => 0.90,
                'bio' => 'Redactora jefe especializada en eficiencia energética y nuevas tecnologías del sector.',
                'verified_at' => now()->subMonths(5),
            ],
            
            // Ecoticias
            [
                'media_outlet_id' => $ecoticias?->id,
                'contact_name' => 'Ricardo Estévez',
                'job_title' => 'Director Editorial',
                'department' => 'Editorial',
                'type' => 'editorial',
                'email' => 'ricardo.estevez@ecoticias.com',
                'phone' => '+34 93 456 7890',
                'specializations' => json_encode(['ecología', 'sostenibilidad', 'biodiversidad']),
                'coverage_areas' => json_encode(['medio ambiente', 'conservación', 'cambio climático']),
                'preferred_contact_method' => 'email',
                'accepts_press_releases' => true,
                'accepts_interviews' => true,
                'accepts_events_invitations' => true,
                'is_freelancer' => false,
                'is_active' => true,
                'is_verified' => true,
                'priority_level' => 3,
                'response_rate' => 0.87,
                'bio' => 'Director editorial de Ecoticias con amplia experiencia en comunicación ambiental y divulgación científica.',
                'verified_at' => now()->subMonths(7),
            ],
        ];

        foreach ($specificContacts as $contact) {
            if ($contact['media_outlet_id']) {
                MediaContact::create($contact);
                echo "✅ Creado contacto: {$contact['contact_name']} - {$contact['job_title']}\n";
            }
        }
    }
}