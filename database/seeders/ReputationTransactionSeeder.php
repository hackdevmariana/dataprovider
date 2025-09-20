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
    public function run(): void
    {
        $this->command->info('🌱 Sembrando transacciones de reputación...');

        // Verificar que existan usuarios
        $users = User::take(10)->get();
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

        // Tipos de transacciones disponibles según el ENUM de la base de datos
        $transactionTypes = [
            'answer_accepted' => [
                'points' => [15],
                'reasons' => [
                    'Respuesta aceptada como solución por el autor de la pregunta',
                    'Solución técnica correcta y bien explicada',
                    'Respuesta completa que resuelve el problema',
                    'Explicación clara y detallada del proceso',
                    'Código funcional y bien documentado',
                ]
            ],
            'answer_upvoted' => [
                'points' => [10],
                'reasons' => [
                    'Respuesta útil votada positivamente por la comunidad',
                    'Explicación técnica precisa y bien estructurada',
                    'Solución práctica y eficiente',
                    'Respuesta educativa y bien fundamentada',
                    'Código limpio y optimizado',
                ]
            ],
            'question_upvoted' => [
                'points' => [5],
                'reasons' => [
                    'Pregunta bien formulada y útil para la comunidad',
                    'Problema técnico interesante y bien descrito',
                    'Pregunta que genera discusión constructiva',
                    'Problema real y bien contextualizado',
                    'Pregunta que ayuda a otros desarrolladores',
                ]
            ],
            'helpful_comment' => [
                'points' => [2],
                'reasons' => [
                    'Comentario útil que aclara un punto importante',
                    'Sugerencia constructiva para mejorar el código',
                    'Explicación adicional que complementa la respuesta',
                    'Comentario que resuelve una duda específica',
                    'Observación técnica relevante',
                ]
            ],
            'tutorial_featured' => [
                'points' => [50],
                'reasons' => [
                    'Tutorial destacado por moderadores por su calidad',
                    'Guía completa y bien estructurada',
                    'Tutorial que cubre un tema complejo de forma clara',
                    'Recurso educativo de alto valor para la comunidad',
                    'Tutorial con ejemplos prácticos y casos de uso',
                ]
            ],
            'project_completed' => [
                'points' => [100],
                'reasons' => [
                    'Proyecto colaborativo completado exitosamente',
                    'Contribución significativa a proyecto comunitario',
                    'Proyecto de código abierto finalizado',
                    'Solución implementada y probada en producción',
                    'Proyecto que beneficia a la comunidad energética',
                ]
            ],
            'expert_verification' => [
                'points' => [500],
                'reasons' => [
                    'Verificación como experto profesional en energía',
                    'Credenciales profesionales verificadas',
                    'Experiencia demostrada en el sector energético',
                    'Certificaciones técnicas reconocidas',
                    'Historial de contribuciones de alta calidad',
                ]
            ],
            'community_award' => [
                'points' => [200],
                'reasons' => [
                    'Premio de la comunidad por contribuciones destacadas',
                    'Reconocimiento por liderazgo en la comunidad',
                    'Premio por mentoring y ayuda a otros usuarios',
                    'Reconocimiento por proyectos innovadores',
                    'Premio por contribuciones consistentes y de calidad',
                ]
            ],
            'first_answer' => [
                'points' => [1],
                'reasons' => [
                    'Primera respuesta en un tema nuevo',
                    'Contribución pionera en discusión',
                    'Respuesta inicial que inicia el debate',
                    'Primera solución propuesta',
                    'Contribución que abre el tema',
                ]
            ],
            'consistency_bonus' => [
                'points' => [10],
                'reasons' => [
                    'Bonus por actividad consistente',
                    'Recompensa por participación regular',
                    'Bonus por contribuciones sostenidas',
                    'Reconocimiento por compromiso continuo',
                    'Bonus por mantenimiento de calidad',
                ]
            ],
            'daily_login' => [
                'points' => [1],
                'reasons' => [
                    'Login diario a la plataforma',
                    'Participación regular en la comunidad',
                    'Mantenimiento de actividad diaria',
                    'Compromiso continuo con la plataforma',
                    'Presencia activa en la comunidad',
                ]
            ],
            'profile_completed' => [
                'points' => [10],
                'reasons' => [
                    'Perfil profesional completado',
                    'Información personal y profesional actualizada',
                    'Credenciales y experiencia documentadas',
                    'Perfil completo con foto y descripción',
                    'Información de contacto y especialización',
                ]
            ],
            'bounty_awarded' => [
                'points' => [25],
                'reasons' => [
                    'Recompensa por respuesta excepcional a bounty',
                    'Solución premium a problema complejo',
                    'Respuesta que supera las expectativas',
                    'Solución innovadora y bien implementada',
                    'Contribución de alto valor a bounty',
                ]
            ],
            'seasonal_bonus' => [
                'points' => [20],
                'reasons' => [
                    'Bonificación estacional por actividad destacada',
                    'Bonus por contribuciones del trimestre',
                    'Recompensa por participación activa',
                    'Bonus por proyectos completados',
                    'Reconocimiento por liderazgo comunitario',
                ]
            ],
            'answer_downvoted' => [
                'points' => [-2],
                'reasons' => [
                    'Respuesta votada negativamente por la comunidad',
                    'Solución incorrecta o mal implementada',
                    'Respuesta que no resuelve el problema',
                    'Código con errores o malas prácticas',
                    'Explicación confusa o incompleta',
                ]
            ],
            'question_downvoted' => [
                'points' => [-2],
                'reasons' => [
                    'Pregunta votada negativamente por la comunidad',
                    'Problema mal descrito o poco claro',
                    'Pregunta que no sigue las guías comunitarias',
                    'Problema duplicado o ya resuelto',
                    'Pregunta que no muestra esfuerzo de investigación',
                ]
            ],
            'spam_detected' => [
                'points' => [-100],
                'reasons' => [
                    'Contenido marcado como spam por moderadores',
                    'Publicidad no autorizada en la plataforma',
                    'Contenido repetitivo y sin valor',
                    'Enlaces maliciosos o no relevantes',
                    'Comportamiento de spam detectado',
                ]
            ],
            'rule_violation' => [
                'points' => [-50],
                'reasons' => [
                    'Violación de reglas comunitarias',
                    'Comportamiento tóxico o inapropiado',
                    'Uso incorrecto de la plataforma',
                    'Incumplimiento de políticas de contenido',
                    'Actividad que va contra los valores comunitarios',
                ]
            ],
            'answer_deleted' => [
                'points' => [-15],
                'reasons' => [
                    'Respuesta eliminada por moderadores',
                    'Contenido que viola las políticas',
                    'Respuesta duplicada o redundante',
                    'Información incorrecta o peligrosa',
                    'Contenido que no cumple los estándares',
                ]
            ],
            'reputation_reversal' => [
                'points' => [0], // Se calcula automáticamente
                'reasons' => [
                    'Transacción revertida por error',
                    'Corrección de penalización incorrecta',
                    'Revisión de moderación',
                    'Apelación aprobada',
                    'Corrección de sistema',
                ]
            ]
        ];

        // Categorías de energía y sostenibilidad
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
            'carbon_footprint' => 'Huella de Carbono',
            'sustainability' => 'Sostenibilidad',
            'climate_change' => 'Cambio Climático',
            'environment' => 'Medio Ambiente',
        ];

        $created = 0;
        $totalReputation = 0;

        foreach ($users as $user) {
            // Obtener reputación actual del usuario
            $userReputation = UserReputation::where('user_id', $user->id)->first();
            $currentReputation = $userReputation ? $userReputation->total_reputation : 0;
            
            // Generar entre 8 y 20 transacciones por usuario
            $numTransactions = rand(8, 20);
            
            $this->command->info("Creando {$numTransactions} transacciones para {$user->name}...");

            for ($i = 0; $i < $numTransactions; $i++) {
                // Seleccionar tipo de transacción aleatorio
                $transactionCategory = array_rand($transactionTypes);
                $pointsChange = $transactionTypes[$transactionCategory]['points'][array_rand($transactionTypes[$transactionCategory]['points'])];
                
                // Para transacciones de penalización, usar puntos negativos
                if (in_array($transactionCategory, ['answer_downvoted', 'question_downvoted', 'spam_detected', 'rule_violation', 'answer_deleted'])) {
                    $pointsChange = abs($pointsChange) * -1; // Asegurar que sea negativo
                }
                
                // Seleccionar categoría aleatoria
                $category = array_rand($energyCategories);
                
                // Seleccionar razón aleatoria
                $reason = $transactionTypes[$transactionCategory]['reasons'][array_rand($transactionTypes[$transactionCategory]['reasons'])];
                
                // Generar fecha aleatoria en los últimos 3 meses
                $randomDate = Carbon::now()->subDays(rand(0, 90));
                
                // Determinar tipo de fuente basado en la categoría de transacción
                $sourceType = $this->getSourceType($transactionCategory);
                $sourceId = rand(1, 1000);
                
                // Determinar si es reversible
                $isReversible = in_array($transactionCategory, ['answer_accepted', 'answer_upvoted', 'question_upvoted', 'helpful_comment', 'tutorial_featured', 'project_completed', 'expert_verification', 'community_award', 'first_answer', 'consistency_bonus', 'daily_login', 'profile_completed', 'bounty_awarded', 'seasonal_bonus']) && rand(0, 10) > 7; // 30% reversible
                
                // Crear transacción directamente con las columnas que existen
                $transaction = ReputationTransaction::create([
                    'user_id' => $user->id,
                    'action_type' => $transactionCategory,
                    'reputation_change' => $pointsChange,
                    'category' => $category,
                    'topic_id' => null,
                    'related_type' => $sourceType,
                    'related_id' => $sourceId,
                    'triggered_by' => $this->getRandomAwarder($users, $user),
                    'description' => $this->getDescription($transactionCategory, $energyCategories[$category]),
                    'metadata' => [
                        'category' => $category,
                        'category_name' => $energyCategories[$category],
                        'source_type' => $sourceType,
                        'user_agent' => 'Seeder Generated',
                        'ip_address' => '127.0.0.1',
                        'session_id' => Str::random(32),
                        'transaction_id' => Str::uuid(),
                        'platform_version' => '1.0.0',
                        'browser_info' => 'Seeder Browser',
                        'reason' => $reason,
                        'points_before' => $currentReputation,
                        'points_after' => $currentReputation + $pointsChange,
                    ],
                    'is_validated' => rand(0, 10) > 1, // 90% validado
                    'is_reversed' => false,
                    'reversed_by' => null,
                    'reversed_at' => null,
                    'reversal_reason' => null,
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);

                $currentReputation += $pointsChange;
                $created++;

                // Actualizar reputación del usuario
                if ($userReputation) {
                    $userReputation->update([
                        'total_reputation' => max(0, $currentReputation), // No permitir reputación negativa
                        'helpful_answers' => $userReputation->helpful_answers + ($transactionCategory === 'answer_upvoted' ? 1 : 0),
                        'accepted_solutions' => $userReputation->accepted_solutions + ($transactionCategory === 'answer_accepted' ? 1 : 0),
                        'upvotes_received' => $userReputation->upvotes_received + ($pointsChange > 0 ? 1 : 0),
                        'downvotes_received' => $userReputation->downvotes_received + ($pointsChange < 0 ? 1 : 0),
                        'quality_posts' => $userReputation->quality_posts + (in_array($transactionCategory, ['answer_accepted', 'answer_upvoted', 'question_upvoted', 'helpful_comment', 'tutorial_featured']) ? 1 : 0),
                        'verified_contributions' => $userReputation->verified_contributions + ($transactionCategory === 'expert_verification' ? 1 : 0),
                    ]);
                }
            }
        }

        // Mostrar estadísticas
        $this->command->info("✅ Transacciones creadas: {$created}");
        $this->command->info("📊 Total de transacciones: " . ReputationTransaction::count());

        // Mostrar resumen por tipo de transacción
        $this->command->info("\n📋 Resumen por tipo de transacción:");
        $types = ReputationTransaction::all()->groupBy('action_type');
        foreach ($types as $type => $transactions) {
            $totalPoints = $transactions->sum('reputation_change');
            $this->command->info("  {$type}: {$transactions->count()} transacciones, {$totalPoints} puntos totales");
        }

        // Mostrar resumen por categoría
        $this->command->info("\n🏷️ Resumen por categoría:");
        $categories = ReputationTransaction::all()->groupBy('category');
        foreach ($categories as $category => $transactions) {
            $this->command->info("  {$category}: {$transactions->count()} transacciones");
        }

        // Mostrar estadísticas por usuario
        $this->command->info("\n👥 Reputación por usuario:");
        foreach ($users as $user) {
            $userReputation = UserReputation::where('user_id', $user->id)->first();
            $transactions = ReputationTransaction::where('user_id', $user->id)->count();
            $reputation = $userReputation ? $userReputation->total_reputation : 0;
            $this->command->info("  {$user->name}: {$reputation} puntos ({$transactions} transacciones)");
        }

        // Mostrar algunas transacciones destacadas
        $this->command->info("\n🔬 Transacciones destacadas:");
        $highlightedTransactions = ReputationTransaction::where('reputation_change', '>', 20)->take(3)->get();
        foreach ($highlightedTransactions as $transaction) {
            $this->command->info("  💰 {$transaction->user->name}: {$transaction->reputation_change} puntos");
            $this->command->info("     📝 {$transaction->description}");
            $this->command->info("     🏷️ {$transaction->category}");
            $this->command->info("     📅 {$transaction->created_at->format('d/m/Y')}");
            $this->command->info("     ---");
        }

        $this->command->info("\n🎯 Seeder de ReputationTransaction completado exitosamente!");
    }

    /**
     * Obtener tipo de fuente basado en el tipo de transacción
     */
    private function getSourceType(string $transactionCategory): string
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
            'first_answer' => ['post', 'answer', 'topic'],
            'consistency_bonus' => ['activity', 'system', 'bonus'],
            'daily_login' => ['login', 'session', 'activity'],
            'profile_completed' => ['profile', 'account', 'user'],
            'bounty_awarded' => ['bounty', 'challenge', 'reward'],
            'seasonal_bonus' => ['bonus', 'seasonal', 'periodic'],
            'answer_downvoted' => ['post', 'answer', 'moderation'],
            'question_downvoted' => ['post', 'question', 'moderation'],
            'spam_detected' => ['moderation', 'system', 'detection'],
            'rule_violation' => ['moderation', 'system', 'violation'],
            'answer_deleted' => ['moderation', 'system', 'deletion'],
            'reputation_reversal' => ['system', 'moderation', 'appeal'],
        ];

        $types = $sourceTypes[$transactionCategory] ?? ['system'];
        return $types[array_rand($types)];
    }

    /**
     * Obtener descripción basada en el tipo de transacción y categoría
     */
    private function getDescription(string $transactionCategory, string $categoryName): string
    {
        $descriptions = [
            'answer_accepted' => "Respuesta aceptada como solución en {$categoryName}",
            'answer_upvoted' => "Respuesta útil votada positivamente en {$categoryName}",
            'question_upvoted' => "Pregunta bien formulada sobre {$categoryName}",
            'helpful_comment' => "Comentario útil en discusión sobre {$categoryName}",
            'tutorial_featured' => "Tutorial destacado sobre {$categoryName}",
            'project_completed' => "Proyecto completado exitosamente en {$categoryName}",
            'expert_verification' => "Verificación como experto en {$categoryName}",
            'community_award' => "Premio de la comunidad por contribuciones en {$categoryName}",
            'first_answer' => "Primera respuesta en tema de {$categoryName}",
            'consistency_bonus' => "Bonus por actividad consistente en {$categoryName}",
            'daily_login' => "Actividad diaria en la plataforma",
            'profile_completed' => "Perfil profesional completado",
            'bounty_awarded' => "Recompensa por respuesta excepcional en {$categoryName}",
            'seasonal_bonus' => "Bonificación estacional por actividad destacada",
            'answer_downvoted' => "Respuesta votada negativamente en {$categoryName}",
            'question_downvoted' => "Pregunta votada negativamente en {$categoryName}",
            'spam_detected' => "Contenido marcado como spam en {$categoryName}",
            'rule_violation' => "Violación de reglas comunitarias en {$categoryName}",
            'answer_deleted' => "Respuesta eliminada por moderadores en {$categoryName}",
            'reputation_reversal' => "Corrección de transacción en {$categoryName}",
        ];

        return $descriptions[$transactionCategory] ?? "Transacción en {$categoryName}";
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