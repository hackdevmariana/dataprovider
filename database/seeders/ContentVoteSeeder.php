<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContentVote;
use App\Models\User;
use App\Models\NewsArticle;
use App\Models\EnergyInstallation;
use App\Models\PlantSpecies;
use App\Models\ProductionRight;
use Carbon\Carbon;

class ContentVoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🗳️ Sembrando votos de contenido...');

        // Obtener usuarios disponibles
        $users = User::take(10)->get();
        if ($users->isEmpty()) {
            $this->command->error('❌ No hay usuarios disponibles para crear ContentVote');
            return;
        }

        // Obtener contenido disponible
        $newsArticles = NewsArticle::take(15)->get();
        $energyInstallations = EnergyInstallation::take(20)->get();
        $plantSpecies = PlantSpecies::take(10)->get();
        $productionRights = ProductionRight::take(8)->get();

        if ($newsArticles->isEmpty() && $energyInstallations->isEmpty() && $plantSpecies->isEmpty() && $productionRights->isEmpty()) {
            $this->command->error('❌ No hay contenido disponible para votar');
            return;
        }

        $createdCount = 0;
        $updatedCount = 0;

        // ===== VOTOS EN ARTÍCULOS DE NOTICIAS =====
        $this->command->info('📰 Generando votos en artículos de noticias...');
        foreach ($newsArticles as $article) {
            $this->generateVotesForContent($article, 'App\Models\NewsArticle', $users, $createdCount, $updatedCount);
        }

        // ===== VOTOS EN INSTALACIONES ENERGÉTICAS =====
        $this->command->info('⚡ Generando votos en instalaciones energéticas...');
        foreach ($energyInstallations as $installation) {
            $this->generateVotesForContent($installation, 'App\Models\EnergyInstallation', $users, $createdCount, $updatedCount);
        }

        // ===== VOTOS EN ESPECIES VEGETALES =====
        $this->command->info('🌱 Generando votos en especies vegetales...');
        foreach ($plantSpecies as $species) {
            $this->generateVotesForContent($species, 'App\Models\PlantSpecies', $users, $createdCount, $updatedCount);
        }

        // ===== VOTOS EN DERECHOS DE PRODUCCIÓN =====
        $this->command->info('🏭 Generando votos en derechos de producción...');
        foreach ($productionRights as $right) {
            $this->generateVotesForContent($right, 'App\Models\ProductionRight', $users, $createdCount, $updatedCount);
        }

        // Mostrar estadísticas
        $this->command->info("✅ Votos creados: {$createdCount}");
        $this->command->info("🔄 Votos actualizados: {$updatedCount}");
        $this->command->info("📊 Total de votos: " . ContentVote::count());

        // Mostrar resumen por tipo de contenido
        $this->command->info("\n📋 Resumen por tipo de contenido:");
        $contentTypes = ContentVote::all()->groupBy('votable_type');
        foreach ($contentTypes as $type => $votes) {
            $upvotes = $votes->where('vote_type', 'upvote')->count();
            $downvotes = $votes->where('vote_type', 'downvote')->count();
            $helpfulVotes = $votes->where('is_helpful_vote', true)->count();
            $this->command->info("  {$type}: {$votes->count()} votos (↑{$upvotes} ↓{$downvotes} 🎯{$helpfulVotes} útiles)");
        }

        // Mostrar resumen por tipo de voto
        $this->command->info("\n🏆 Resumen por tipo de voto:");
        $voteTypes = ContentVote::all()->groupBy('vote_type');
        foreach ($voteTypes as $type => $votes) {
            $avgWeight = $votes->avg('vote_weight');
            $helpfulCount = $votes->where('is_helpful_vote', true)->count();
            $this->command->info("  {$type}: {$votes->count()} votos, peso medio: " . number_format($avgWeight, 1) . ", útiles: {$helpfulCount}");
        }

        // Mostrar resumen por peso de voto
        $this->command->info("\n⚖️ Resumen por peso de voto:");
        $weightGroups = [
            '1' => ContentVote::where('vote_weight', 1)->count(),
            '2-5' => ContentVote::whereBetween('vote_weight', [2, 5])->count(),
            '6-10' => ContentVote::whereBetween('vote_weight', [6, 10])->count(),
            '11+' => ContentVote::where('vote_weight', '>', 10)->count(),
        ];
        foreach ($weightGroups as $range => $count) {
            $this->command->info("  Peso {$range}: {$count} votos");
        }

        // Mostrar algunos votos destacados
        $this->command->info("\n🔬 Votos destacados:");
        $featuredVotes = ContentVote::where('is_helpful_vote', true)
            ->orWhere('vote_weight', '>', 5)
            ->take(5)
            ->get();
        
        foreach ($featuredVotes as $vote) {
            $contentType = class_basename($vote->votable_type);
            $voteType = $vote->vote_type === 'upvote' ? '👍' : '👎';
            $weight = $vote->vote_weight > 1 ? " (peso: {$vote->vote_weight})" : '';
            $helpful = $vote->is_helpful_vote ? ' 🎯' : '';
            $this->command->info("  {$voteType} {$contentType} - Usuario: {$vote->user->name}{$weight}{$helpful}");
        }

        // Mostrar estadísticas de validación
        $this->command->info("\n✅ Estadísticas de validación:");
        $totalVotes = ContentVote::count();
        $validVotes = ContentVote::where('is_valid', true)->count();
        $invalidVotes = ContentVote::count() - $validVotes;
        $validatedVotes = ContentVote::whereNotNull('validated_by')->count();
        
        $this->command->info("  🟢 Votos válidos: {$validVotes} (" . round(($validVotes/$totalVotes)*100, 1) . "%)");
        $this->command->info("  🔴 Votos inválidos: {$invalidVotes} (" . round(($invalidVotes/$totalVotes)*100, 1) . "%)");
        $this->command->info("  🔍 Votos validados: {$validatedVotes} (" . round(($validatedVotes/$totalVotes)*100, 1) . "%)");

        $this->command->info("\n🎯 Seeder de ContentVote completado exitosamente!");
    }

    /**
     * Generar votos para un contenido específico.
     */
    private function generateVotesForContent($content, string $contentType, $users, int &$createdCount, int &$updatedCount): void
    {
        // Número de votos a generar (entre 3 y 8 por contenido)
        $voteCount = rand(3, 8);
        
        // Seleccionar usuarios aleatorios para votar
        $votingUsers = $users->random(min($voteCount, $users->count()));
        
        foreach ($votingUsers as $user) {
            // Determinar tipo de voto (70% upvote, 30% downvote)
            $voteType = rand(1, 100) <= 70 ? 'upvote' : 'downvote';
            
            // Calcular peso del voto basado en la reputación del usuario
            $voteWeight = $this->calculateVoteWeight($user);
            
            // Determinar si es un voto útil (20% de probabilidad)
            $isHelpfulVote = rand(1, 100) <= 20;
            
            // Generar razón para downvotes
            $reason = $voteType === 'downvote' ? $this->generateDownvoteReason() : null;
            
            // Generar metadata
            $metadata = $this->generateMetadata($content, $voteType, $isHelpfulVote);
            
            // Determinar si el voto es válido (95% de probabilidad)
            $isValid = rand(1, 100) <= 95;
            
            // Asignar validador para algunos votos
            $validatedBy = $isValid && rand(1, 100) <= 30 ? $users->random()->id : null;
            
            // Crear o actualizar el voto
            $vote = ContentVote::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'votable_type' => $contentType,
                    'votable_id' => $content->id,
                ],
                [
                    'vote_type' => $voteType,
                    'vote_weight' => $voteWeight,
                    'reason' => $reason,
                    'is_helpful_vote' => $isHelpfulVote,
                    'metadata' => $metadata,
                    'is_valid' => $isValid,
                    'validated_by' => $validatedBy,
                ]
            );

            if ($vote->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }
    }

    /**
     * Calcular el peso del voto basado en la reputación del usuario.
     */
    private function calculateVoteWeight($user): int
    {
        // Simular reputación del usuario (en un sistema real vendría de la tabla reputation)
        $reputation = rand(1, 1000);
        
        if ($reputation >= 800) {
            return rand(8, 12); // Usuarios con alta reputación
        } elseif ($reputation >= 500) {
            return rand(5, 8);  // Usuarios con reputación media-alta
        } elseif ($reputation >= 200) {
            return rand(3, 6);  // Usuarios con reputación media
        } elseif ($reputation >= 50) {
            return rand(2, 4);  // Usuarios con reputación baja-media
        } else {
            return 1;            // Usuarios nuevos
        }
    }

    /**
     * Generar razón para downvotes.
     */
    private function generateDownvoteReason(): string
    {
        $reasons = [
            'Contenido irrelevante o fuera de tema',
            'Información incorrecta o desactualizada',
            'Calidad del contenido insuficiente',
            'Formato o presentación deficiente',
            'Contenido duplicado o repetitivo',
            'Falta de fuentes o referencias',
            'Contenido ofensivo o inapropiado',
            'Errores gramaticales o ortográficos',
            'Información incompleta o superficial',
            'No cumple las directrices de la comunidad',
        ];
        
        return $reasons[array_rand($reasons)];
    }

    /**
     * Generar metadata para el voto.
     */
    private function generateMetadata($content, string $voteType, bool $isHelpfulVote): array
    {
        $metadata = [
            'voting_context' => $this->getVotingContext($content),
            'user_experience_level' => $this->getUserExperienceLevel(),
            'voting_session_duration' => rand(5, 120), // segundos
            'device_type' => $this->getDeviceType(),
            'browser_info' => $this->getBrowserInfo(),
        ];

        if ($voteType === 'downvote') {
            $metadata['downvote_categories'] = $this->getDownvoteCategories();
            $metadata['suggested_improvements'] = $this->getSuggestedImprovements();
        }

        if ($isHelpfulVote) {
            $metadata['helpful_reason'] = $this->getHelpfulReason();
            $metadata['content_quality_score'] = rand(7, 10);
        }

        return $metadata;
    }

    /**
     * Obtener contexto de votación basado en el tipo de contenido.
     */
    private function getVotingContext($content): string
    {
        $className = class_basename($content);
        
        return match($className) {
            'NewsArticle' => 'news_browsing',
            'EnergyInstallation' => 'energy_research',
            'PlantSpecies' => 'sustainability_study',
            'ProductionRight' => 'energy_market_analysis',
            default => 'general_browsing'
        };
    }

    /**
     * Obtener nivel de experiencia del usuario.
     */
    private function getUserExperienceLevel(): string
    {
        $levels = ['beginner', 'intermediate', 'advanced', 'expert'];
        $weights = [30, 40, 20, 10]; // Probabilidades
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($levels as $index => $level) {
            $cumulative += $weights[$index];
            if ($random <= $cumulative) {
                return $level;
            }
        }
        
        return 'intermediate';
    }

    /**
     * Obtener tipo de dispositivo.
     */
    private function getDeviceType(): string
    {
        $devices = ['desktop', 'mobile', 'tablet'];
        $weights = [60, 30, 10]; // Probabilidades
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($devices as $index => $device) {
            $cumulative += $weights[$index];
            if ($random <= $cumulative) {
                return $device;
            }
        }
        
        return 'desktop';
    }

    /**
     * Obtener información del navegador.
     */
    private function getBrowserInfo(): array
    {
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge'];
        $browser = $browsers[array_rand($browsers)];
        
        return [
            'name' => $browser,
            'version' => rand(80, 120),
            'platform' => $this->getPlatform(),
        ];
    }

    /**
     * Obtener plataforma del usuario.
     */
    private function getPlatform(): string
    {
        $platforms = ['Windows', 'macOS', 'Linux', 'Android', 'iOS'];
        $weights = [50, 20, 15, 10, 5]; // Probabilidades
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($platforms as $index => $platform) {
            $cumulative += $weights[$index];
            if ($random <= $cumulative) {
                return $platform;
            }
        }
        
        return 'Windows';
    }

    /**
     * Obtener categorías de downvote.
     */
    private function getDownvoteCategories(): array
    {
        $categories = [
            'content_quality',
            'relevance',
            'accuracy',
            'completeness',
            'formatting',
            'originality'
        ];
        
        $selectedCount = rand(1, 3);
        $selectedKeys = array_rand(array_flip($categories), $selectedCount);
        
        // Asegurar que siempre devuelva un array
        if (is_array($selectedKeys)) {
            return $selectedKeys;
        } else {
            return [$selectedKeys];
        }
    }

    /**
     * Obtener sugerencias de mejora.
     */
    private function getSuggestedImprovements(): array
    {
        $improvements = [
            'Añadir más detalles técnicos',
            'Incluir ejemplos prácticos',
            'Mejorar la estructura del contenido',
            'Añadir referencias y fuentes',
            'Actualizar información obsoleta',
            'Corregir errores gramaticales',
            'Mejorar la presentación visual',
            'Añadir conclusiones claras',
        ];
        
        $selectedCount = rand(1, 3);
        $selectedKeys = array_rand(array_flip($improvements), $selectedCount);
        
        // Asegurar que siempre devuelva un array
        if (is_array($selectedKeys)) {
            return $selectedKeys;
        } else {
            return [$selectedKeys];
        }
    }

    /**
     * Obtener razón del voto útil.
     */
    private function getHelpfulReason(): string
    {
        $reasons = [
            'Información muy útil y bien explicada',
            'Contenido de alta calidad y relevante',
            'Excelente presentación y formato',
            'Información actualizada y precisa',
            'Muy bien documentado y referenciado',
            'Contenido educativo y bien estructurado',
            'Información práctica y aplicable',
            'Excelente trabajo de investigación',
        ];
        
        return $reasons[array_rand($reasons)];
    }
}
