<?php

namespace Database\Seeders;

use App\Models\DailyAnniversary;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DailyAnniversarySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creando aniversarios diarios...');

        $anniversaries = [
            [
                'title' => 'Descubrimiento de América',
                'description' => 'Cristóbal Colón llegó a las costas de América, marcando el inicio de la era de la exploración europea del Nuevo Mundo.',
                'years_ago' => 531,
                'original_date' => '1492-10-12',
                'category' => 'historical',
                'type' => 'exploration',
                'related_people' => json_encode(['Cristóbal Colón', 'Isabel I de Castilla', 'Fernando II de Aragón']),
                'related_places' => json_encode(['Guanahani', 'Bahamas', 'España']),
                'significance' => 'critical',
                'is_recurring' => true,
                'celebration_info' => json_encode(['Día de la Hispanidad', 'Fiesta nacional en España']),
            ],
            [
                'title' => 'Toma de la Bastilla',
                'description' => 'El pueblo de París tomó la fortaleza de la Bastilla, símbolo del absolutismo monárquico, iniciando la Revolución Francesa.',
                'years_ago' => 235,
                'original_date' => '1789-07-14',
                'category' => 'historical',
                'type' => 'revolution',
                'related_people' => json_encode(['Robespierre', 'Danton', 'Marat']),
                'related_places' => json_encode(['París', 'Bastilla', 'Francia']),
                'significance' => 'critical',
                'is_recurring' => true,
                'celebration_info' => json_encode(['Fiesta Nacional de Francia', 'Día de la Bastilla']),
            ],
            [
                'title' => 'Primer Hombre en la Luna',
                'description' => 'Neil Armstrong se convirtió en el primer ser humano en pisar la superficie lunar durante la misión Apolo 11.',
                'years_ago' => 55,
                'original_date' => '1969-07-20',
                'category' => 'scientific',
                'type' => 'space_exploration',
                'related_people' => json_encode(['Neil Armstrong', 'Buzz Aldrin', 'Michael Collins']),
                'related_places' => json_encode(['Luna', 'Mar de la Tranquilidad', 'Estados Unidos']),
                'significance' => 'major',
                'is_recurring' => true,
                'celebration_info' => json_encode(['Día de la Luna', 'Celebración espacial']),
            ],
            [
                'title' => 'Caída del Muro de Berlín',
                'description' => 'El Muro de Berlín fue derribado, simbolizando el fin de la Guerra Fría y la reunificación de Alemania.',
                'years_ago' => 35,
                'original_date' => '1989-11-09',
                'category' => 'political',
                'type' => 'reunification',
                'related_people' => json_encode(['Mijaíl Gorbachov', 'Helmut Kohl', 'Egon Krenz']),
                'related_places' => json_encode(['Berlín', 'Alemania', 'Europa del Este']),
                'significance' => 'critical',
                'is_recurring' => true,
                'celebration_info' => json_encode(['Día de la Unidad Alemana', 'Fiesta de la Libertad']),
            ],
            [
                'title' => 'Reforma Protestante',
                'description' => 'Martín Lutero publicó sus 95 tesis en Wittenberg, iniciando la Reforma Protestante que dividió la cristiandad occidental.',
                'years_ago' => 506,
                'original_date' => '1517-10-31',
                'category' => 'religious',
                'type' => 'reformation',
                'related_people' => json_encode(['Martín Lutero', 'Juan Calvino', 'Ulrico Zuinglio']),
                'related_places' => json_encode(['Wittenberg', 'Alemania', 'Europa']),
                'significance' => 'critical',
                'is_recurring' => true,
                'celebration_info' => json_encode(['Día de la Reforma', 'Halloween']),
            ],
            [
                'title' => 'Día de la Victoria en Europa',
                'description' => 'Alemania se rindió incondicionalmente, poniendo fin a la Segunda Guerra Mundial en Europa.',
                'years_ago' => 79,
                'original_date' => '1945-05-08',
                'category' => 'historical',
                'type' => 'war_end',
                'related_people' => json_encode(['Winston Churchill', 'Harry Truman', 'Charles de Gaulle']),
                'related_places' => json_encode(['Europa', 'Berlín', 'Reims']),
                'significance' => 'critical',
                'is_recurring' => true,
                'celebration_info' => json_encode(['Día de la Victoria', 'VE Day']),
            ],
            [
                'title' => 'Marcha sobre Washington',
                'description' => 'Martin Luther King Jr. pronunció su famoso discurso "I Have a Dream" durante la marcha por los derechos civiles.',
                'years_ago' => 61,
                'original_date' => '1963-08-28',
                'category' => 'social',
                'type' => 'civil_rights',
                'related_people' => json_encode(['Martin Luther King Jr.', 'John Lewis', 'Bayard Rustin']),
                'related_places' => json_encode(['Washington D.C.', 'Lincoln Memorial', 'Estados Unidos']),
                'significance' => 'major',
                'is_recurring' => true,
                'celebration_info' => json_encode(['Día de los Derechos Civiles', 'Marcha por la Libertad']),
            ],
            [
                'title' => 'Atentados del 11 de Septiembre',
                'description' => 'Ataques terroristas contra las Torres Gemelas y el Pentágono que cambiaron el mundo para siempre.',
                'years_ago' => 23,
                'original_date' => '2001-09-11',
                'category' => 'political',
                'type' => 'terrorist_attack',
                'related_people' => json_encode(['George W. Bush', 'Rudolph Giuliani', 'Osama bin Laden']),
                'related_places' => json_encode(['Nueva York', 'Pentágono', 'Estados Unidos']),
                'significance' => 'critical',
                'is_recurring' => true,
                'celebration_info' => json_encode(['Día de Recordación', 'Patriot Day']),
            ],
        ];

        $count = 0;
        foreach ($anniversaries as $anniversaryData) {
            DailyAnniversary::create($anniversaryData);
            $count++;
        }

        // Crear aniversarios adicionales aleatorios
        $categories = ['historical', 'cultural', 'religious', 'scientific', 'political', 'social', 'artistic', 'sports', 'literary', 'musical'];
        $types = ['birth', 'death', 'discovery', 'invention', 'war', 'peace', 'revolution', 'independence', 'achievement', 'milestone'];
        $significanceLevels = ['critical', 'major', 'important', 'notable', 'minor'];

        for ($i = 0; $i < 12; $i++) {
            $year = fake()->numberBetween(1000, now()->year);
            $yearsAgo = now()->year - $year;
            
            // Generar fecha válida
            $month = fake()->numberBetween(1, 12);
            $maxDay = match($month) {
                2 => 28, // Febrero (año no bisiesto para simplificar)
                4, 6, 9, 11 => 30, // Abril, junio, septiembre, noviembre
                default => 31 // Enero, marzo, mayo, julio, agosto, octubre, diciembre
            };
            $day = fake()->numberBetween(1, $maxDay);
            
            DailyAnniversary::create([
                'title' => fake()->sentence(3),
                'description' => fake()->paragraph(2),
                'years_ago' => $yearsAgo,
                'original_date' => $year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $day),
                'category' => fake()->randomElement($categories),
                'type' => fake()->randomElement($types),
                'related_people' => json_encode(fake()->randomElements(['Persona 1', 'Persona 2', 'Persona 3'], rand(1, 3))),
                'related_places' => json_encode(fake()->randomElements(['Lugar 1', 'Lugar 2'], rand(1, 2))),
                'significance' => fake()->randomElement($significanceLevels),
                'is_recurring' => fake()->boolean(80),
                'celebration_info' => json_encode(fake()->randomElements(['Celebración 1', 'Celebración 2'], rand(1, 2))),
            ]);
            $count++;
        }

        $this->command->info("✅ Creados {$count} aniversarios diarios");
        $this->showStatistics();
    }

    private function showStatistics(): void
    {
        $total = DailyAnniversary::count();
        $recurring = DailyAnniversary::where('is_recurring', true)->count();
        
        $this->command->info("\n📊 Estadísticas:");
        $this->command->info("   Total aniversarios: {$total}");
        $this->command->info("   Recurrentes: {$recurring}");
        
        $categories = DailyAnniversary::selectRaw('category, COUNT(*) as count')->groupBy('category')->get();
        $this->command->info("\n📅 Por categoría:");
        foreach ($categories as $category) {
            $this->command->info("   {$category->category}: {$category->count}");
        }
        
        $significance = DailyAnniversary::selectRaw('significance, COUNT(*) as count')->groupBy('significance')->get();
        $this->command->info("\n⭐ Por significancia:");
        foreach ($significance as $sig) {
            $this->command->info("   {$sig->significance}: {$sig->count}");
        }
        
        $types = DailyAnniversary::selectRaw('type, COUNT(*) as count')->groupBy('type')->get();
        $this->command->info("\n🏷️ Por tipo:");
        foreach ($types as $type) {
            $this->command->info("   {$type->type}: {$type->count}");
        }
    }
}