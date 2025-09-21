<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ListItem;
use App\Models\User;
use Carbon\Carbon;

class ListItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(20)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecuta primero UserSeeder.');
            return;
        }

        $addedModes = ['manual', 'auto_hashtag', 'auto_keyword', 'auto_author', 'suggested', 'imported'];
        $statuses = ['active', 'pending', 'rejected', 'archived'];
        $listableTypes = ['App\\Models\\Post', 'App\\Models\\User', 'App\\Models\\ProjectProposal', 'App\\Models\\CooperativePost'];
        $tags = ['energía', 'renovable', 'solar', 'eólica', 'sostenibilidad', 'medio ambiente', 'tecnología', 'innovación', 'comunidad', 'proyecto'];

        $created = 0;
        $maxAttempts = 200;
        $attempts = 0;
        
        while ($created < 80 && $attempts < $maxAttempts) {
            $attempts++;
            
            $userId = $users->random()->id;
            $listableType = $listableTypes[array_rand($listableTypes)];
            $listableId = rand(1, 50);
            $userListId = rand(1, 20);
            
            // Verificar si ya existe esta combinación
            $existing = ListItem::where('user_list_id', $userListId)
                ->where('listable_type', $listableType)
                ->where('listable_id', $listableId)
                ->exists();
                
            if ($existing) {
                continue; // Saltar si ya existe
            }
            
            ListItem::create([
                'user_list_id' => $userListId,
                'listable_type' => $listableType,
                'listable_id' => $listableId,
                'added_by' => $userId,
                'position' => rand(1, 100),
                'personal_note' => rand(0, 1) ? 'Nota personal: ' . fake()->sentence() : null,
                'tags' => array_rand(array_flip($tags), rand(1, 4)),
                'personal_rating' => rand(0, 1) ? rand(1, 50) / 10 : null, // 1.0 a 5.0
                'added_mode' => $addedModes[array_rand($addedModes)],
                'status' => $statuses[array_rand($statuses)],
                'reviewed_by' => rand(0, 1) ? $users->random()->id : null,
                'reviewed_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null,
                'clicks_count' => rand(0, 100),
                'likes_count' => rand(0, 50),
                'last_accessed_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 7)) : null,
            ]);
            $created++;
        }
    }
}
