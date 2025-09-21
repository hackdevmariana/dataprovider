<?php

namespace Database\Seeders;

use App\Models\UserReview;
use App\Models\User;
use App\Models\Organization;
use App\Models\EnergyCompany;
use App\Models\EnergyInstallation;
use App\Models\Cooperative;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios existentes
        $users = User::take(20)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles para crear reviews. Ejecuta UserSeeder primero.');
            return;
        }

        // Obtener entidades reviewables
        $organizations = Organization::take(10)->get();
        $energyCompanies = EnergyCompany::take(10)->get();
        $energyInstallations = EnergyInstallation::take(10)->get();
        $cooperatives = Cooperative::take(10)->get();

        $reviewableEntities = collect()
            ->merge($organizations->map(fn($org) => ['type' => Organization::class, 'id' => $org->id, 'name' => $org->name]))
            ->merge($energyCompanies->map(fn($company) => ['type' => EnergyCompany::class, 'id' => $company->id, 'name' => $company->name]))
            ->merge($energyInstallations->map(fn($installation) => ['type' => EnergyInstallation::class, 'id' => $installation->id, 'name' => $installation->name]))
            ->merge($cooperatives->map(fn($coop) => ['type' => Cooperative::class, 'id' => $coop->id, 'name' => $coop->name]));

        if ($reviewableEntities->isEmpty()) {
            $this->command->warn('No hay entidades disponibles para reviewar. Crea algunas entidades primero.');
            return;
        }

        $serviceTypes = [
            'installation',
            'maintenance',
            'consulting',
            'design',
            'financing',
            'legal_advice',
            'training',
            'product_sale',
            'project_management',
            'community_service',
            'platform_experience',
            'other'
        ];

        $statuses = ['published', 'pending_review', 'flagged', 'hidden', 'rejected', 'disputed'];
        $titles = [
            'Excelente servicio de instalación solar',
            'Muy satisfecho con la atención al cliente',
            'Proceso de instalación muy profesional',
            'Calidad del servicio superó mis expectativas',
            'Equipo técnico muy competente',
            'Precio justo por el servicio recibido',
            'Tiempo de respuesta muy rápido',
            'Servicio post-venta excepcional',
            'Instalación realizada sin problemas',
            'Recomiendo ampliamente este servicio'
        ];

        $this->command->info('Creando reviews de usuarios...');

        for ($i = 0; $i < 30; $i++) {
            $reviewer = $users->random();
            $entity = $reviewableEntities->random();
            
            $overallRating = fake()->randomFloat(1, 1.0, 5.0);
            $serviceType = fake()->randomElement($serviceTypes);
            $status = fake()->randomElement($statuses);
            $title = fake()->randomElement($titles);
            
            // Crear ratings detallados
            $detailedRatings = [
                'quality' => fake()->randomFloat(1, 1.0, 5.0),
                'service' => fake()->randomFloat(1, 1.0, 5.0),
                'communication' => fake()->randomFloat(1, 1.0, 5.0),
                'timeliness' => fake()->randomFloat(1, 1.0, 5.0),
                'value_for_money' => fake()->randomFloat(1, 1.0, 5.0),
            ];

            $pros = [
                fake()->randomElement(['Precio competitivo', 'Atención al cliente excelente', 'Tiempo de instalación rápido']),
                fake()->randomElement(['Equipo técnico profesional', 'Materiales de calidad', 'Garantía extendida'])
            ];

            $cons = fake()->boolean(60) ? [
                fake()->randomElement(['Tiempo de espera largo', 'Comunicación inicial confusa', 'Precio un poco alto'])
            ] : [];

            // Generar contenido de review
            $content = "Basándome en mi experiencia con este servicio, puedo decir que ";
            
            if ($overallRating >= 4.0) {
                $content .= "ha sido muy positiva. ";
            } elseif ($overallRating >= 3.0) {
                $content .= "ha sido satisfactoria en general. ";
            } else {
                $content .= "ha tenido algunas áreas de mejora. ";
            }

            if (!empty($pros)) {
                $content .= "Los aspectos más destacados incluyen: " . implode(', ', $pros) . ". ";
            }

            if (!empty($cons)) {
                $content .= "Sin embargo, también noté: " . implode(', ', $cons) . ". ";
            }

            $content .= "En general, " . ($overallRating >= 3.5 ? "recomiendo este servicio" : "considero que hay margen de mejora") . ".";

            $review = UserReview::create([
                'reviewer_id' => $reviewer->id,
                'reviewable_type' => $entity['type'],
                'reviewable_id' => $entity['id'],
                'overall_rating' => $overallRating,
                'detailed_ratings' => $detailedRatings,
                'title' => $title,
                'content' => $content,
                'pros' => $pros,
                'cons' => $cons,
                'service_type' => $serviceType,
                'service_date' => fake()->dateTimeBetween('-1 year', 'now'),
                'service_cost' => fake()->randomFloat(2, 100, 5000),
                'service_location' => fake()->city() . ', ' . fake()->country(),
                'service_duration_days' => fake()->numberBetween(1, 30),
                'is_verified_purchase' => fake()->boolean(70),
                'verification_code' => fake()->boolean(70) ? fake()->uuid() : null,
                'verified_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-6 months', 'now') : null,
                'verified_by' => fake()->boolean(70) ? $users->random()->id : null,
                'would_recommend' => fake()->boolean(80),
                'recommendation_level' => fake()->numberBetween(1, 5),
                'helpful_votes' => fake()->numberBetween(0, 25),
                'not_helpful_votes' => fake()->numberBetween(0, 5),
                'total_votes' => fake()->numberBetween(0, 30),
                'helpfulness_ratio' => fake()->randomFloat(2, 0, 100),
                'views_count' => fake()->numberBetween(0, 100),
                'provider_response' => fake()->boolean(30) ? fake()->paragraph(3) : null,
                'provider_responded_at' => fake()->boolean(30) ? fake()->dateTimeBetween('-3 months', 'now') : null,
                'provider_responder_id' => fake()->boolean(30) ? $users->random()->id : null,
                'status' => $status,
                'flags_count' => fake()->numberBetween(0, 2),
                'moderated_by' => fake()->boolean(20) ? $users->random()->id : null,
                'moderated_at' => fake()->boolean(20) ? fake()->dateTimeBetween('-2 months', 'now') : null,
                'moderation_notes' => fake()->boolean(20) ? fake()->sentence() : null,
                'is_anonymous' => fake()->boolean(15),
                'show_service_cost' => fake()->boolean(60),
                'allow_contact' => fake()->boolean(70),
            ]);

            // Actualizar ratio de utilidad si hay votos
            if ($review->total_votes > 0) {
                $review->updateHelpfulnessRatio();
            }
        }

        $this->command->info('✅ UserReviewSeeder completado: 30 reviews creadas');
    }
}