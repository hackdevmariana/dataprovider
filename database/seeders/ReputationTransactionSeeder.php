<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReputationTransaction;
use App\Models\User;
use App\Models\UserReputation;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReputationTransactionSeeder extends Seeder
{
    public function run()
    {
        // Verificar que existan usuarios
        $users = User::take(5)->get();
        if ($users->isEmpty()) {
            $this->command->error('No se encontraron usuarios. Ejecuta primero el UserSeeder.');
            return;
        }

        $this->command->info("Creando transacciones de reputación para {$users->count()} usuarios...");

        // Crear o actualizar reputaciones de usuario si no existen
        foreach ($users as $user) {
            UserReputation::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'total_reputation' => 0,
                    'category_reputation' => [],
                    'topic_reputation' => [],
                    'helpful_answers' => 0,
                    'accepted_solutions' => 0,
                    'quality_posts' => 0,
                    'verified_contributions' => 0,
                    'upvotes_received' => 0,
                    'downvotes_received' => 0,
                    'upvote_ratio' => 0.00,
                    'topics_created' => 0,
                    'successful_projects' => 0,
                    'mentorship_points' => 0,
                    'warnings_received' => 0,
                    'content_removed' => 0,
                    'is_suspended' => false,
                    'is_verified_professional' => false,
                ]
            );
        }

        // Tipos de transacciones disponibles según el modelo
        $transactionTypes = [
            'answer_accepted' => 15,
            'answer_upvoted' => 10,
            'question_upvoted' => 5,
            'helpful_comment' => 2,
            'tutorial_featured' => 50,
            'project_completed' => 100,
            'expert_verification' => 500,
            'community_award' => 200,
            'first_answer' => 1,
            'consistency_bonus' => 10,
            'daily_login' => 1,
            'profile_completed' => 10,
            'bounty_awarded' => 25,
            'seasonal_bonus' => 20,
            'answer_downvoted' => -2,
            'question_downvoted' => -2,
            'spam_detected' => -100,
            'rule_violation' => -50,
            'answer_deleted' => -15,
        ];

        // Categorías de energía
        $energyCategories = [
            'solar' => 'Energía Solar',
            'wind' => 'Energía Eólica',
            'hydro' => 'Energía Hidroeléctrica',
            'nuclear' => 'Energía Nuclear',
            'biomass' => 'Biomasa',
            'geothermal' => 'Energía Geotérmica',
            'efficiency' => 'Eficiencia Energética',
            'storage' => 'Almacenamiento de Energía',
            'grid' => 'Red Eléctrica',
            'renewable' => 'Energías Renovables',
        ];

        // Razones para las transacciones
        $reasons = [
            'answer_accepted' => [
                'Respuesta aceptada como solución por el autor de la pregunta',
                'Solución técnica correcta y bien explicada',
                'Respuesta completa que resuelve el problema',
                'Explicación clara y detallada del proceso',
                'Código funcional y bien documentado',
            ],
            'answer_upvoted' => [
                'Respuesta útil votada positivamente por la comunidad',
                'Explicación técnica precisa y bien estructurada',
                'Solución práctica y eficiente',
                'Respuesta educativa y bien fundamentada',
                'Código limpio y optimizado',
            ],
            'question_upvoted' => [
                'Pregunta bien formulada y útil para la comunidad',
                'Problema técnico interesante y bien descrito',
                'Pregunta que genera discusión constructiva',
                'Problema real y bien contextualizado',
                'Pregunta que ayuda a otros desarrolladores',
            ],
            'helpful_comment' => [
                'Comentario útil que aclara un punto importante',
                'Sugerencia constructiva para mejorar el código',
                'Explicación adicional que complementa la respuesta',
                'Comentario que resuelve una duda específica',
                'Observación técnica relevante',
            ],
            'tutorial_featured' => [
                'Tutorial destacado por moderadores por su calidad',
                'Guía completa y bien estructurada',
                'Tutorial que cubre un tema complejo de forma clara',
                'Recurso educativo de alto valor para la comunidad',
                'Tutorial con ejemplos prácticos y casos de uso',
            ],
            'project_completed' => [
                'Proyecto colaborativo completado exitosamente',
                'Contribución significativa a proyecto comunitario',
                'Proyecto de código abierto finalizado',
                'Solución implementada y probada en producción',
                'Proyecto que beneficia a la comunidad energética',
            ],
            'expert_verification' => [
                'Verificación como experto profesional en energía',
                'Credenciales profesionales verificadas',
                'Experiencia demostrada en el sector energético',
                'Certificaciones técnicas reconocidas',
                'Historial de contribuciones de alta calidad',
            ],
            'community_award' => [
                'Premio de la comunidad por contribuciones destacadas',
                'Reconocimiento por liderazgo en la comunidad',
                'Premio por mentoring y ayuda a otros usuarios',
                'Reconocimiento por proyectos innovadores',
                'Premio por contribuciones consistentes y de calidad',
            ],
            'daily_login' => [
                'Login diario a la plataforma',
                'Participación regular en la comunidad',
                'Mantenimiento de actividad diaria',
                'Compromiso continuo con la plataforma',
                'Presencia activa en la comunidad',
            ],
            'profile_completed' => [
                'Perfil profesional completado',
                'Información personal y profesional actualizada',
                'Credenciales y experiencia documentadas',
                'Perfil completo con foto y descripción',
                'Información de contacto y especialización',
            ],
            'bounty_awarded' => [
                'Recompensa por respuesta excepcional a bounty',
                'Solución premium a problema complejo',
                'Respuesta que supera las expectativas',
                'Solución innovadora y bien implementada',
                'Contribución de alto valor a bounty',
            ],
            'seasonal_bonus' => [
                'Bonificación estacional por actividad destacada',
                'Bonus por contribuciones del trimestre',
                'Recompensa por participación activa',
                'Bonus por proyectos completados',
                'Reconocimiento por liderazgo comunitario',
            ],
            'answer_downvoted' => [
                'Respuesta votada negativamente por la comunidad',
                'Solución incorrecta o mal implementada',
                'Respuesta que no resuelve el problema',
                'Código con errores o malas prácticas',
                'Explicación confusa o incompleta',
            ],
            'question_downvoted' => [
                'Pregunta votada negativamente por la comunidad',
                'Problema mal descrito o poco claro',
                'Pregunta que no sigue las guías comunitarias',
                'Problema duplicado o ya resuelto',
                'Pregunta que no muestra esfuerzo de investigación',
            ],
            'spam_detected' => [
                'Contenido marcado como spam por moderadores',
                'Publicidad no autorizada en la plataforma',
                'Contenido repetitivo y sin valor',
                'Enlaces maliciosos o no relevantes',
                'Comportamiento de spam detectado',
            ],
            'rule_violation' => [
                'Violación de reglas comunitarias',
                'Comportamiento tóxico o inapropiado',
                'Uso incorrecto de la plataforma',
                'Incumplimiento de políticas de contenido',
                'Actividad que va contra los valores comunitarios',
            ],
            'answer_deleted' => [
                'Respuesta eliminada por moderadores',
                'Contenido que viola las políticas',
                'Respuesta duplicada o redundante',
                'Información incorrecta o peligrosa',
                'Contenido que no cumple los estándares',
            ],
            'first_answer' => [
                'Primera respuesta en un tema nuevo',
                'Contribución pionera en discusión',
                'Respuesta inicial que inicia el debate',
                'Primera solución propuesta',
                'Contribución que abre el tema',
            ],
            'consistency_bonus' => [
                'Bonus por actividad consistente',
                'Recompensa por participación regular',
                'Bonus por contribuciones sostenidas',
                'Reconocimiento por compromiso continuo',
                'Bonus por mantenimiento de calidad',
            ],
        ];

        $created = 0;
        $totalReputation = 0;

        foreach ($users as $user) {
            // Generar entre 5 y 15 transacciones por usuario
            $numTransactions = rand(5, 15);
            
            $this->command->info("Creando {$numTransactions} transacciones para {$user->name}...");

            for ($i = 0; $i < $numTransactions; $i++) {
                // Seleccionar tipo de transacción aleatorio
                $transactionType = array_rand($transactionTypes);
                $pointsChange = $transactionTypes[$transactionType];
                
                // Seleccionar categoría aleatoria
                $category = array_rand($energyCategories);
                
                // Seleccionar razón aleatoria
                $reason = $reasons[$transactionType][array_rand($reasons[$transactionType])] ?? 'Transacción de reputación';
                
                // Generar fecha aleatoria en los últimos 6 meses
                $randomDate = Carbon::now()->subDays(rand(0, 180));
                
                // Crear transacción
                $transaction = ReputationTransaction::create([
                    'user_id' => $user->id,
                    'action_type' => $transactionType,
                    'reputation_change' => $pointsChange,
                    'category' => $category,
                    'topic_id' => null, // No hay tabla topics en el sistema actual
                    'related_type' => $this->getRandomSourceType($transactionType),
                    'related_id' => rand(1, 1000),
                    'triggered_by' => $this->getRandomAwarder($users, $user),
                    'description' => $this->getDescription($transactionType, $category),
                    'metadata' => [
                        'category' => $category,
                        'source_type' => $this->getRandomSourceType($transactionType),
                        'user_agent' => 'Seeder Generated',
                        'ip_address' => '127.0.0.1',
                        'session_id' => Str::random(32),
                        'reason' => $reason,
                        'points_before' => $totalReputation,
                        'points_after' => $totalReputation + $pointsChange,
                    ],
                    'is_validated' => rand(0, 10) > 1, // 90% validado
                    'is_reversed' => false,
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);

                $totalReputation += $pointsChange;
                $created++;

                // Actualizar reputación del usuario
                $userReputation = UserReputation::where('user_id', $user->id)->first();
                if ($userReputation) {
                    $userReputation->update([
                        'total_reputation' => $totalReputation,
                        'helpful_answers' => $userReputation->helpful_answers + ($transactionType === 'answer_upvoted' ? 1 : 0),
                        'accepted_solutions' => $userReputation->accepted_solutions + ($transactionType === 'answer_accepted' ? 1 : 0),
                        'upvotes_received' => $userReputation->upvotes_received + (in_array($transactionType, ['answer_upvoted', 'question_upvoted']) ? 1 : 0),
                        'downvotes_received' => $userReputation->downvotes_received + (in_array($transactionType, ['answer_downvoted', 'question_downvoted']) ? 1 : 0),
                    ]);
                }
            }
        }

        $this->command->info("🎯 Resumen del seeder:");
        $this->command->info("   - Transacciones creadas: {$created}");
        $this->command->info("   - Usuarios procesados: {$users->count()}");
        $this->command->info("   - Reputación total generada: {$totalReputation}");
        
        // Mostrar estadísticas por usuario
        $this->command->info("📊 Reputación por usuario:");
        foreach ($users as $user) {
            $userReputation = UserReputation::where('user_id', $user->id)->first();
            $transactions = ReputationTransaction::where('user_id', $user->id)->count();
            $reputation = $userReputation ? $userReputation->total_reputation : 0;
            $this->command->info("   - {$user->name}: {$reputation} puntos ({$transactions} transacciones)");
        }
    }

    /**
     * Obtener tipo de fuente aleatorio basado en el tipo de transacción
     */
    private function getRandomSourceType(string $transactionType): string
    {
        $sourceTypes = [
            'answer_accepted' => ['post', 'answer', 'solution'],
            'answer_upvoted' => ['post', 'answer', 'comment'],
            'question_upvoted' => ['post', 'question', 'topic'],
            'helpful_comment' => ['comment', 'reply', 'note'],
            'tutorial_featured' => ['tutorial', 'guide', 'documentation'],
            'project_completed' => ['project', 'repository', 'collaboration'],
            'expert_verification' => ['verification', 'credential', 'certification'],
            'community_award' => ['award', 'recognition', 'achievement'],
            'daily_login' => ['login', 'session', 'activity'],
            'profile_completed' => ['profile', 'account', 'user'],
            'bounty_awarded' => ['bounty', 'challenge', 'reward'],
            'seasonal_bonus' => ['bonus', 'seasonal', 'periodic'],
        ];

        $types = $sourceTypes[$transactionType] ?? ['post', 'activity', 'system'];
        return $types[array_rand($types)];
    }

    /**
     * Obtener descripción basada en el tipo de transacción y categoría
     */
    private function getDescription(string $transactionType, string $category): string
    {
        $descriptions = [
            'answer_accepted' => "Respuesta aceptada como solución en el área de {$category}",
            'answer_upvoted' => "Respuesta útil votada positivamente en {$category}",
            'question_upvoted' => "Pregunta bien formulada sobre {$category}",
            'helpful_comment' => "Comentario útil en discusión sobre {$category}",
            'tutorial_featured' => "Tutorial destacado sobre {$category}",
            'project_completed' => "Proyecto completado exitosamente en {$category}",
            'expert_verification' => "Verificación como experto en {$category}",
            'community_award' => "Premio de la comunidad por contribuciones en {$category}",
            'daily_login' => "Actividad diaria en la plataforma",
            'profile_completed' => "Perfil profesional completado",
            'bounty_awarded' => "Recompensa por respuesta excepcional en {$category}",
            'seasonal_bonus' => "Bonificación estacional por actividad destacada",
        ];

        return $descriptions[$transactionType] ?? "Transacción de reputación en {$category}";
    }

    /**
     * Obtener usuario que otorga la reputación (diferente al usuario que la recibe)
     */
    private function getRandomAwarder($users, $currentUser): ?int
    {
        $otherUsers = $users->where('id', '!=', $currentUser->id);
        if ($otherUsers->isEmpty()) {
            return null;
        }
        
        return $otherUsers->random()->id;
    }
}
