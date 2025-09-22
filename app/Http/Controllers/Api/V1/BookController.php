<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

#[OA\Tag(name: "Books")]
/**
 * @OA\Tag(
 *     name="Libros",
 *     description="APIs para la gestión de Libros"
 * )
 */
class BookController extends Controller
{
    /**
     * Display a listing of books.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('author')) {
            $query->where('author', 'like', "%{$request->author}%");
        }

        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }

        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        if ($request->filled('year_from')) {
            $query->where('publication_year', '>=', $request->year_from);
        }

        if ($request->filled('year_to')) {
            $query->where('publication_year', '<=', $request->year_to);
        }

        $sortBy = $request->get('sort_by', 'title');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = min($request->get('per_page', 15), 100);
        $books = $query->paginate($perPage);

        return response()->json([
            'data' => $books->items(),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ]
        ]);
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books,isbn',
            'genre' => 'nullable|string|max:100',
            'language' => 'required|string|max:10',
            'publication_year' => 'nullable|integer|min:1|max:' . date('Y'),
            'publisher' => 'nullable|string|max:255',
            'pages' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:2000',
            'cover_image_url' => 'nullable|url|max:500',
            'rating' => 'nullable|numeric|between:0,5',
            'is_available' => 'boolean',
        ]);

        $book = Book::create($validated);

        return response()->json([
            'data' => $book,
            'message' => 'Libro creado exitosamente'
        ], 201);
    }

    /**
     * Display the specified book.
     */
    public function show(Book $book): JsonResponse
    {
        return response()->json([
            'data' => $book
        ]);
    }

    /**
     * Update the specified book.
     */
    public function update(Request $request, Book $book): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
            'genre' => 'nullable|string|max:100',
            'language' => 'sometimes|required|string|max:10',
            'publication_year' => 'nullable|integer|min:1|max:' . date('Y'),
            'publisher' => 'nullable|string|max:255',
            'pages' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:2000',
            'cover_image_url' => 'nullable|url|max:500',
            'rating' => 'nullable|numeric|between:0,5',
            'is_available' => 'boolean',
        ]);

        $book->update($validated);

        return response()->json([
            'data' => $book->fresh(),
            'message' => 'Libro actualizado exitosamente'
        ]);
    }

    /**
     * Remove the specified book.
     */
    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return response()->json([
            'message' => 'Libro eliminado exitosamente'
        ]);
    }

    /**
     * Get books by author.
     */
    public function byAuthor(Request $request, string $author): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $books = Book::where('author', 'like', "%{$author}%")
            ->orderBy('publication_year', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $books->items(),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
                'author' => $author,
            ]
        ]);
    }

    /**
     * Get books by genre.
     */
    public function byGenre(Request $request, string $genre): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $books = Book::where('genre', $genre)
            ->orderBy('rating', 'desc')
            ->orderBy('title', 'asc')
            ->paginate($perPage);

        return response()->json([
            'data' => $books->items(),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
                'genre' => $genre,
            ]
        ]);
    }

    /**
     * Get statistics for books.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_books' => Book::count(),
            'available_books' => Book::where('is_available', true)->count(),
            'books_by_genre' => Book::selectRaw('genre, COUNT(*) as count')
                ->whereNotNull('genre')
                ->groupBy('genre')
                ->orderBy('count', 'desc')
                ->get(),
            'books_by_language' => Book::selectRaw('language, COUNT(*) as count')
                ->groupBy('language')
                ->orderBy('count', 'desc')
                ->get(),
            'books_by_year' => Book::selectRaw('publication_year, COUNT(*) as count')
                ->whereNotNull('publication_year')
                ->groupBy('publication_year')
                ->orderBy('publication_year', 'desc')
                ->limit(10)
                ->get(),
            'average_rating' => round(Book::whereNotNull('rating')->avg('rating'), 2),
            'top_rated_books' => Book::whereNotNull('rating')
                ->orderBy('rating', 'desc')
                ->limit(10)
                ->get(['id', 'title', 'author', 'rating']),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}