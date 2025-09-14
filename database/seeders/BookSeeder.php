<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear libros específicos y realistas
        $books = [
            [
                'title' => 'Don Quijote de la Mancha',
                'original_title' => 'El ingenioso hidalgo don Quijote de la Mancha',
                'synopsis' => 'Las aventuras de un hidalgo manchego que perdió el juicio leyendo libros de caballerías.',
                'author' => 'Miguel de Cervantes',
                'isbn' => '978-84-376-0494-7',
                'publisher' => 'Cátedra',
                'publication_date' => '1605-01-01',
                'language' => 'es',
                'genre' => 'Novela',
                'pages' => 863,
                'format' => 'Tapa blanda',
                'rating' => 4.5,
                'ratings_count' => 1250,
                'reviews_count' => 89,
                'awards' => 'Premio Cervantes',
                'tags' => 'clásico, literatura española, caballería',
                'cover_image' => 'https://via.placeholder.com/300x400/1e40af/ffffff?text=Don+Quijote',
            ],
            [
                'title' => 'Cien años de soledad',
                'original_title' => 'Cien años de soledad',
                'synopsis' => 'La historia de la familia Buendía a lo largo de siete generaciones en el pueblo ficticio de Macondo.',
                'author' => 'Gabriel García Márquez',
                'isbn' => '978-84-376-0495-4',
                'publisher' => 'Cátedra',
                'publication_date' => '1967-01-01',
                'language' => 'es',
                'genre' => 'Realismo mágico',
                'pages' => 471,
                'format' => 'Tapa blanda',
                'rating' => 4.7,
                'ratings_count' => 2100,
                'reviews_count' => 156,
                'awards' => 'Premio Nobel de Literatura',
                'tags' => 'realismo mágico, literatura latinoamericana, Macondo',
                'cover_image' => 'https://via.placeholder.com/300x400/059669/ffffff?text=Cien+años',
            ],
            [
                'title' => 'El amor en los tiempos del cólera',
                'original_title' => 'El amor en los tiempos del cólera',
                'synopsis' => 'Una historia de amor que dura más de cincuenta años entre Fermina Daza y Florentino Ariza.',
                'author' => 'Gabriel García Márquez',
                'isbn' => '978-84-376-0496-1',
                'publisher' => 'Cátedra',
                'publication_date' => '1985-01-01',
                'language' => 'es',
                'genre' => 'Novela romántica',
                'pages' => 464,
                'format' => 'Tapa blanda',
                'rating' => 4.3,
                'ratings_count' => 1800,
                'reviews_count' => 124,
                'awards' => null,
                'tags' => 'amor, literatura latinoamericana, romance',
                'cover_image' => 'https://via.placeholder.com/300x400/dc2626/ffffff?text=Amor+cólera',
            ],
            [
                'title' => 'La sombra del viento',
                'original_title' => 'La sombra del viento',
                'synopsis' => 'Un niño descubre un libro misterioso que cambiará su vida para siempre en el Barcelona de posguerra.',
                'author' => 'Carlos Ruiz Zafón',
                'isbn' => '978-84-376-0497-8',
                'publisher' => 'Planeta',
                'publication_date' => '2001-01-01',
                'language' => 'es',
                'genre' => 'Misterio',
                'pages' => 576,
                'format' => 'Tapa dura',
                'rating' => 4.4,
                'ratings_count' => 3200,
                'reviews_count' => 245,
                'awards' => 'Premio Edebé',
                'tags' => 'misterio, Barcelona, literatura española',
                'cover_image' => 'https://via.placeholder.com/300x400/7c3aed/ffffff?text=Sombra+viento',
            ],
            [
                'title' => 'El tiempo entre costuras',
                'original_title' => 'El tiempo entre costuras',
                'synopsis' => 'La historia de Sira, una modista que se ve envuelta en intrigas políticas durante la Guerra Civil española.',
                'author' => 'María Dueñas',
                'isbn' => '978-84-376-0498-5',
                'publisher' => 'Temas de Hoy',
                'publication_date' => '2009-01-01',
                'language' => 'es',
                'genre' => 'Novela histórica',
                'pages' => 624,
                'format' => 'Tapa blanda',
                'rating' => 4.2,
                'ratings_count' => 2800,
                'reviews_count' => 198,
                'awards' => null,
                'tags' => 'historia, Guerra Civil, moda, espionaje',
                'cover_image' => 'https://via.placeholder.com/300x400/b45309/ffffff?text=Tiempo+costuras',
            ],
        ];

        // Insertar los libros
        foreach ($books as $book) {
            Book::updateOrCreate(
                ['isbn' => $book['isbn']], // Buscar por ISBN único
                $book
            );
        }

        // Crear algunos libros adicionales
        $additionalBooks = [
            [
                'title' => 'El Principito',
                'original_title' => 'Le Petit Prince',
                'synopsis' => 'Un cuento poético que trata temas profundos como el sentido de la vida, la soledad, la amistad, el amor y la pérdida.',
                'author' => 'Antoine de Saint-Exupéry',
                'isbn' => '978-84-376-0499-2',
                'publisher' => 'Salamandra',
                'publication_date' => '1943-01-01',
                'language' => 'es',
                'genre' => 'Cuento',
                'pages' => 96,
                'format' => 'Tapa dura',
                'rating' => 4.8,
                'ratings_count' => 5000,
                'reviews_count' => 350,
                'awards' => null,
                'tags' => 'infantil, filosofía, clásico',
                'cover_image' => 'https://via.placeholder.com/300x400/0f766e/ffffff?text=Principito',
            ],
        ];

        foreach ($additionalBooks as $book) {
            Book::updateOrCreate(
                ['isbn' => $book['isbn']],
                $book
            );
        }

        $this->command->info('Libros creados exitosamente.');
    }
}