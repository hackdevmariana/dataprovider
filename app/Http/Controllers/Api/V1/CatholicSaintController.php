<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\CatholicSaint;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * Controlador para la gestión de Santos Católicos
 * 
 * Este controlador proporciona endpoints para gestionar el santoral católico,
 * incluyendo operaciones CRUD, búsquedas especializadas, filtros por fechas
 * y funcionalidades específicas para calendarios litúrgicos.
 * 
 * @package App\Http\Controllers
 * @author Sistema de Gestión de Datos
 * @version 1.0.0
 * @since 2024
 */
class CatholicSaintController extends \App\Http\Controllers\Controller
{
    /**
     * Constructor del controlador
     * 
     * Aplica middleware de autenticación y autorización
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'today', 'byDate', 'byCategory', 'search']);
        $this->middleware('permission:view catholic-saints')->only(['index', 'show', 'today', 'byDate', 'byCategory', 'search']);
        $this->middleware('permission:create catholic-saints')->only(['store']);
        $this->middleware('permission:edit catholic-saints')->only(['update']);
        $this->middleware('permission:delete catholic-saints')->only(['destroy']);
    }

    /**
     * Muestra una lista paginada de todos los santos católicos
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @api {get} /api/catholic-saints Listar santos católicos
     * @apiName GetCatholicSaints
     * @apiGroup CatholicSaints
     * @apiVersion 1.0.0
     * 
     * @apiParam {Number} [page=1] Número de página
     * @apiParam {Number} [per_page=15] Elementos por página
     * @apiParam {String} [search] Término de búsqueda
     * @apiParam {String} [category] Filtrar por categoría
     * @apiParam {String} [feast_type] Filtrar por tipo de celebración
     * @apiParam {Boolean} [is_active] Filtrar por estado activo
     * @apiParam {Boolean} [is_patron] Filtrar por patronos
     * @apiParam {String} [sort_by=feast_date] Campo de ordenamiento
     * @apiParam {String} [sort_direction=asc] Dirección del ordenamiento
     * 
     * @apiSuccess {Object[]} data Lista de santos católicos
     * @apiSuccess {Number} data.id ID del santo
     * @apiSuccess {String} data.name Nombre del santo
     * @apiSuccess {String} data.category Categoría del santo
     * @apiSuccess {String} data.feast_date Fecha de celebración
     * @apiSuccess {String} data.feast_type Tipo de celebración
     * @apiSuccess {Boolean} data.is_active Estado activo
     * @apiSuccess {Boolean} data.is_patron Es patrono
     * @apiSuccess {Number} data.popularity_score Puntuación de popularidad
     * @apiSuccess {Object} data.municipality Municipio patrono
     * @apiSuccess {Object} links Enlaces de paginación
     * @apiSuccess {Object} meta Metadatos de paginación
     * 
     * @apiExample {curl} Ejemplo de uso:
     *     curl -i http://localhost:8000/api/catholic-saints?page=1&per_page=20&category=martyr
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validar parámetros de entrada
            $validator = Validator::make($request->all(), [
                'page' => 'integer|min:1',
                'per_page' => 'integer|min:1|max:100',
                'search' => 'string|max:255',
                'category' => 'string|in:martyr,confessor,virgin,virgin_martyr,bishop,pope,religious,lay_person,founder,doctor,apostle,evangelist,prophet,patriarch,other',
                'feast_type' => 'string|in:solemnity,feast,memorial,optional_memorial,commemoration',
                'is_active' => 'boolean',
                'is_patron' => 'boolean',
                'sort_by' => 'string|in:id,name,category,feast_date,feast_type,popularity_score,created_at',
                'sort_direction' => 'string|in:asc,desc',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parámetros de validación incorrectos',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            // Construir query base
            $query = CatholicSaint::with(['municipality', 'birthPlace', 'deathPlace']);

            // Aplicar filtros
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('canonical_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('patron_of', 'like', "%{$search}%");
                });
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('feast_type')) {
                $query->where('feast_type', $request->feast_type);
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->has('is_patron')) {
                $query->where('is_patron', $request->boolean('is_patron'));
            }

            // Aplicar ordenamiento
            $sortBy = $request->get('sort_by', 'feast_date');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $saints = $query->paginate($perPage);

            // Cache de resultados para mejorar rendimiento
            $cacheKey = 'catholic_saints_' . md5(serialize($request->all()));
            Cache::put($cacheKey, $saints, now()->addMinutes(30));

            return response()->json([
                'success' => true,
                'message' => 'Santos católicos obtenidos exitosamente',
                'data' => $saints->items(),
                'links' => [
                    'first' => $saints->url(1),
                    'last' => $saints->url($saints->lastPage()),
                    'prev' => $saints->previousPageUrl(),
                    'next' => $saints->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $saints->currentPage(),
                    'last_page' => $saints->lastPage(),
                    'per_page' => $saints->perPage(),
                    'total' => $saints->total(),
                    'from' => $saints->firstItem(),
                    'to' => $saints->lastItem(),
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            \Log::error('Error en CatholicSaintController@index: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Muestra un santo católico específico
     * 
     * @param CatholicSaint $catholicSaint
     * @return JsonResponse
     * 
     * @api {get} /api/catholic-saints/{id} Obtener santo católico
     * @apiName GetCatholicSaint
     * @apiGroup CatholicSaints
     * @apiVersion 1.0.0
     * 
     * @apiParam {Number} id ID único del santo
     * 
     * @apiSuccess {Object} data Datos del santo católico
     * @apiSuccess {Number} data.id ID del santo
     * @apiSuccess {String} data.name Nombre del santo
     * @apiSuccess {String} data.canonical_name Nombre canónico
     * @apiSuccess {String} data.description Descripción breve
     * @apiSuccess {String} data.biography Biografía completa
     * @apiSuccess {String} data.birth_date Fecha de nacimiento
     * @apiSuccess {String} data.death_date Fecha de muerte
     * @apiSuccess {String} data.canonization_date Fecha de canonización
     * @apiSuccess {String} data.feast_date Fecha de celebración
     * @apiSuccess {String} data.category Categoría
     * @apiSuccess {String} data.feast_type Tipo de celebración
     * @apiSuccess {String} data.liturgical_color Color litúrgico
     * @apiSuccess {String} data.patron_of Patrono de
     * @apiSuccess {Boolean} data.is_patron Es patrono
     * @apiSuccess {Object} data.patronages Patronazgos específicos
     * @apiSuccess {String} data.specialties Especialidades
     * @apiSuccess {Object} data.municipality Municipio patrono
     * @apiSuccess {Object} data.birth_place Lugar de nacimiento
     * @apiSuccess {Object} data.death_place Lugar de muerte
     * @apiSuccess {String} data.region Región
     * @apiSuccess {String} data.country País
     * @apiSuccess {String} data.liturgical_rank Rango litúrgico
     * @apiSuccess {String} data.prayers Oraciones
     * @apiSuccess {String} data.hymns Himnos
     * @apiSuccess {Object} data.attributes Atributos y símbolos
     * @apiSuccess {Boolean} data.is_active Santo activo
     * @apiSuccess {Boolean} data.is_universal Celebrado universalmente
     * @apiSuccess {Boolean} data.is_local Solo local
     * @apiSuccess {Number} data.popularity_score Puntuación de popularidad
     * @apiSuccess {String} data.notes Notas adicionales
     * @apiSuccess {String} data.created_at Fecha de creación
     * @apiSuccess {String} data.updated_at Fecha de actualización
     */
    public function show(CatholicSaint $catholicSaint): JsonResponse
    {
        try {
            // Cargar relaciones
            $catholicSaint->load(['municipality', 'birthPlace', 'deathPlace']);

            // Incrementar contador de visitas (si se implementa)
            // $catholicSaint->increment('views_count');

            return response()->json([
                'success' => true,
                'message' => 'Santo católico obtenido exitosamente',
                'data' => $catholicSaint
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            \Log::error('Error en CatholicSaintController@show: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Almacena un nuevo santo católico
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @api {post} /api/catholic-saints Crear santo católico
     * @apiName CreateCatholicSaint
     * @apiGroup CatholicSaints
     * @apiVersion 1.0.0
     * 
     * @apiParam {String} name Nombre del santo (requerido)
     * @apiParam {String} [canonical_name] Nombre canónico
     * @apiParam {String} slug Slug único (requerido)
     * @apiParam {String} [description] Descripción breve
     * @apiParam {String} [biography] Biografía completa
     * @apiParam {String} [birth_date] Fecha de nacimiento
     * @apiParam {String} [death_date] Fecha de muerte
     * @apiParam {String} [canonization_date] Fecha de canonización
     * @apiParam {String} feast_date Fecha de celebración (requerido)
     * @apiParam {String} [feast_date_optional] Fecha alternativa
     * @apiParam {String} category Categoría (requerido)
     * @apiParam {String} feast_type Tipo de celebración (requerido)
     * @apiParam {String} [liturgical_color] Color litúrgico
     * @apiParam {String} [patron_of] Patrono de
     * @apiParam {Boolean} [is_patron] Es patrono
     * @apiParam {Object} [patronages] Patronazgos específicos
     * @apiParam {String} [specialties] Especialidades
     * @apiParam {Number} [birth_place_id] ID del lugar de nacimiento
     * @apiParam {Number} [death_place_id] ID del lugar de muerte
     * @apiParam {Number} [municipality_id] ID del municipio patrono
     * @apiParam {String} [region] Región
     * @apiParam {String} [country] País
     * @apiParam {String} [liturgical_rank] Rango litúrgico
     * @apiParam {String} [prayers] Oraciones
     * @apiParam {String} [hymns] Himnos
     * @apiParam {Object} [attributes] Atributos y símbolos
     * @apiParam {Boolean} [is_active] Santo activo
     * @apiParam {Boolean} [is_universal] Celebrado universalmente
     * @apiParam {Boolean} [is_local] Solo local
     * @apiParam {Number} [popularity_score] Puntuación de popularidad
     * @apiParam {String} [notes] Notas adicionales
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'canonical_name' => 'nullable|string|max:255',
                'slug' => 'required|string|max:255|unique:catholic_saints,slug',
                'description' => 'nullable|string|max:500',
                'biography' => 'nullable|string',
                'birth_date' => 'nullable|date',
                'death_date' => 'nullable|date|after_or_equal:birth_date',
                'canonization_date' => 'nullable|date|after_or_equal:death_date',
                'feast_date' => 'required|date',
                'feast_date_optional' => 'nullable|date',
                'category' => 'required|string|in:martyr,confessor,virgin,virgin_martyr,bishop,pope,religious,lay_person,founder,doctor,apostle,evangelist,prophet,patriarch,other',
                'feast_type' => 'required|string|in:solemnity,feast,memorial,optional_memorial,commemoration',
                'liturgical_color' => 'nullable|string|in:white,red,green,purple,pink,gold,black',
                'patron_of' => 'nullable|string',
                'is_patron' => 'boolean',
                'patronages' => 'nullable|array',
                'specialties' => 'nullable|string',
                'birth_place_id' => 'nullable|exists:municipalities,id',
                'death_place_id' => 'nullable|exists:municipalities,id',
                'municipality_id' => 'nullable|exists:municipalities,id',
                'region' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'liturgical_rank' => 'nullable|string|max:255',
                'prayers' => 'nullable|string',
                'hymns' => 'nullable|string',
                'attributes' => 'nullable|array',
                'is_active' => 'boolean',
                'is_universal' => 'boolean',
                'is_local' => 'boolean',
                'popularity_score' => 'nullable|integer|min:0|max:10',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            // Crear el santo católico
            $catholicSaint = CatholicSaint::create($request->all());

            // Cargar relaciones para la respuesta
            $catholicSaint->load(['municipality', 'birthPlace', 'deathPlace']);

            // Limpiar caché relacionado
            Cache::forget('catholic_saints_*');

            return response()->json([
                'success' => true,
                'message' => 'Santo católico creado exitosamente',
                'data' => $catholicSaint
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            \Log::error('Error en CatholicSaintController@store: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Actualiza un santo católico existente
     * 
     * @param Request $request
     * @param CatholicSaint $catholicSaint
     * @return JsonResponse
     * 
     * @api {put} /api/catholic-saints/{id} Actualizar santo católico
     * @apiName UpdateCatholicSaint
     * @apiGroup CatholicSaints
     * @apiVersion 1.0.0
     */
    public function update(Request $request, CatholicSaint $catholicSaint): JsonResponse
    {
        try {
            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'canonical_name' => 'nullable|string|max:255',
                'slug' => 'sometimes|required|string|max:255|unique:catholic_saints,slug,' . $catholicSaint->id,
                'description' => 'nullable|string|max:500',
                'biography' => 'nullable|string',
                'birth_date' => 'nullable|date',
                'death_date' => 'nullable|date|after_or_equal:birth_date',
                'canonization_date' => 'nullable|date|after_or_equal:death_date',
                'feast_date' => 'sometimes|required|date',
                'feast_date_optional' => 'nullable|date',
                'category' => 'sometimes|required|string|in:martyr,confessor,virgin,virgin_martyr,bishop,pope,religious,lay_person,founder,doctor,apostle,evangelist,prophet,patriarch,other',
                'feast_type' => 'sometimes|required|string|in:solemnity,feast,memorial,optional_memorial,commemoration',
                'liturgical_color' => 'nullable|string|in:white,red,green,purple,pink,gold,black',
                'patron_of' => 'nullable|string',
                'is_patron' => 'boolean',
                'patronages' => 'nullable|array',
                'specialties' => 'nullable|string',
                'birth_place_id' => 'nullable|exists:municipalities,id',
                'death_place_id' => 'nullable|exists:municipalities,id',
                'municipality_id' => 'nullable|exists:municipalities,id',
                'region' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'liturgical_rank' => 'nullable|string|max:255',
                'prayers' => 'nullable|string',
                'hymns' => 'nullable|string',
                'attributes' => 'nullable|array',
                'is_active' => 'boolean',
                'is_universal' => 'boolean',
                'is_local' => 'boolean',
                'popularity_score' => 'nullable|integer|min:0|max:10',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            // Actualizar el santo católico
            $catholicSaint->update($request->all());

            // Cargar relaciones para la respuesta
            $catholicSaint->load(['municipality', 'birthPlace', 'deathPlace']);

            // Limpiar caché relacionado
            Cache::forget('catholic_saints_*');

            return response()->json([
                'success' => true,
                'message' => 'Santo católico actualizado exitosamente',
                'data' => $catholicSaint
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            \Log::error('Error en CatholicSaintController@update: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Elimina un santo católico
     * 
     * @param CatholicSaint $catholicSaint
     * @return JsonResponse
     * 
     * @api {delete} /api/catholic-saints/{id} Eliminar santo católico
     * @apiName DeleteCatholicSaint
     * @apiGroup CatholicSaints
     * @apiVersion 1.0.0
     */
    public function destroy(CatholicSaint $catholicSaint): JsonResponse
    {
        try {
            // Verificar si se puede eliminar (por ejemplo, si no tiene relaciones críticas)
            // if ($catholicSaint->hasRelatedRecords()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'No se puede eliminar el santo católico porque tiene registros relacionados'
            //     ], Response::HTTP_CONFLICT);
            // }

            $catholicSaint->delete();

            // Limpiar caché relacionado
            Cache::forget('catholic_saints_*');

            return response()->json([
                'success' => true,
                'message' => 'Santo católico eliminado exitosamente'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            \Log::error('Error en CatholicSaintController@destroy: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtiene el santo del día actual
     * 
     * @return JsonResponse
     * 
     * @api {get} /api/catholic-saints/today Santo del día
     * @apiName GetTodaySaint
     * @apiGroup CatholicSaints
     * @apiVersion 1.0.0
     */
    public function today(): JsonResponse
    {
        try {
            $today = Carbon::today();
            
            $saint = CatholicSaint::where('is_active', true)
                ->where(function (Builder $query) use ($today) {
                    $query->whereRaw('DATE(feast_date) = ?', [$today->format('Y-m-d')])
                          ->orWhereRaw('DATE(feast_date_optional) = ?', [$today->format('Y-m-d')]);
                })
                ->with(['municipality', 'birthPlace', 'deathPlace'])
                ->orderBy('feast_type', 'desc') // Priorizar solemnidades
                ->first();

            if (!$saint) {
                return response()->json([
                    'success' => true,
                    'message' => 'No hay santo del día para hoy',
                    'data' => null,
                    'date' => $today->format('Y-m-d')
                ], Response::HTTP_OK);
            }

            return response()->json([
                'success' => true,
                'message' => 'Santo del día obtenido exitosamente',
                'data' => $saint,
                'date' => $today->format('Y-m-d')
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            \Log::error('Error en CatholicSaintController@today: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtiene santos por fecha específica
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @api {get} /api/catholic-saints/by-date Santos por fecha
     * @apiName GetSaintsByDate
     * @apiGroup CatholicSaints
     * @apiVersion 1.0.0
     * 
     * @apiParam {String} date Fecha en formato Y-m-d
     */
    public function byDate(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'date' => 'required|date_format:Y-m-d'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de fecha incorrecto. Use Y-m-d',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            $date = Carbon::createFromFormat('Y-m-d', $request->date);
            
            $saints = CatholicSaint::where('is_active', true)
                ->where(function (Builder $query) use ($date) {
                    $query->whereRaw('DATE(feast_date) = ?', [$date->format('Y-m-d')])
                          ->orWhereRaw('DATE(feast_date_optional) = ?', [$date->format('Y-m-d')]);
                })
                ->with(['municipality', 'birthPlace', 'deathPlace'])
                ->orderBy('feast_type', 'desc')
                ->orderBy('popularity_score', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Santos por fecha obtenidos exitosamente',
                'data' => $saints,
                'date' => $date->format('Y-m-d'),
                'count' => $saints->count()
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            \Log::error('Error en CatholicSaintController@byDate: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtiene santos por categoría
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @api {get} /api/catholic-saints/by-category Santos por categoría
     * @apiName GetSaintsByCategory
     * @apiGroup CatholicSaints
     * @apiVersion 1.0.0
     * 
     * @apiParam {String} category Categoría del santo
     * @apiParam {Number} [page=1] Número de página
     * @apiParam {Number} [per_page=15] Elementos por página
     */
    public function byCategory(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'category' => 'required|string|in:martyr,confessor,virgin,virgin_martyr,bishop,pope,religious,lay_person,founder,doctor,apostle,evangelist,prophet,patriarch,other',
                'page' => 'integer|min:1',
                'per_page' => 'integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parámetros de validación incorrectos',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            $perPage = $request->get('per_page', 15);
            
            $saints = CatholicSaint::where('category', $request->category)
                ->where('is_active', true)
                ->with(['municipality', 'birthPlace', 'deathPlace'])
                ->orderBy('popularity_score', 'desc')
                ->orderBy('name', 'asc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Santos por categoría obtenidos exitosamente',
                'data' => $saints->items(),
                'category' => $request->category,
                'links' => [
                    'first' => $saints->url(1),
                    'last' => $saints->url($saints->lastPage()),
                    'prev' => $saints->previousPageUrl(),
                    'next' => $saints->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $saints->currentPage(),
                    'last_page' => $saints->lastPage(),
                    'per_page' => $saints->perPage(),
                    'total' => $saints->total(),
                    'from' => $saints->firstItem(),
                    'to' => $saints->lastItem(),
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            \Log::error('Error en CatholicSaintController@byCategory: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Búsqueda avanzada de santos
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @api {get} /api/catholic-saints/search Búsqueda avanzada
     * @apiName SearchSaints
     * @apiGroup CatholicSaints
     * @apiVersion 1.0.0
     * 
     * @apiParam {String} [q] Término de búsqueda
     * @apiParam {String} [category] Filtrar por categoría
     * @apiParam {String} [feast_type] Filtrar por tipo de celebración
     * @apiParam {String} [liturgical_color] Filtrar por color litúrgico
     * @apiParam {Boolean} [is_patron] Filtrar por patronos
     * @apiParam {String} [region] Filtrar por región
     * @apiParam {String} [country] Filtrar por país
     * @apiParam {Number} [min_popularity] Puntuación mínima de popularidad
     * @apiParam {Number} [max_popularity] Puntuación máxima de popularidad
     * @apiParam {String} [date_from] Fecha de celebración desde
     * @apiParam {String} [date_to] Fecha de celebración hasta
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'q' => 'nullable|string|max:255',
                'category' => 'nullable|string|in:martyr,confessor,virgin,virgin_martyr,bishop,pope,religious,lay_person,founder,doctor,apostle,evangelist,prophet,patriarch,other',
                'feast_type' => 'nullable|string|in:solemnity,feast,memorial,optional_memorial,commemoration',
                'liturgical_color' => 'nullable|string|in:white,red,green,purple,pink,gold,black',
                'is_patron' => 'nullable|boolean',
                'region' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'min_popularity' => 'nullable|integer|min:0|max:10',
                'max_popularity' => 'nullable|integer|min:0|max:10',
                'date_from' => 'nullable|date_format:Y-m-d',
                'date_to' => 'nullable|date_format:Y-m-d',
                'page' => 'integer|min:1',
                'per_page' => 'integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parámetros de validación incorrectos',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            $query = CatholicSaint::with(['municipality', 'birthPlace', 'deathPlace']);

            // Aplicar filtros de búsqueda
            if ($request->filled('q')) {
                $searchTerm = $request->q;
                $query->where(function (Builder $q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('canonical_name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhere('biography', 'like', "%{$searchTerm}%")
                      ->orWhere('patron_of', 'like', "%{$searchTerm}%")
                      ->orWhere('specialties', 'like', "%{$searchTerm}%");
                });
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('feast_type')) {
                $query->where('feast_type', $request->feast_type);
            }

            if ($request->filled('liturgical_color')) {
                $query->where('liturgical_color', $request->liturgical_color);
            }

            if ($request->has('is_patron')) {
                $query->where('is_patron', $request->boolean('is_patron'));
            }

            if ($request->filled('region')) {
                $query->where('region', 'like', "%{$request->region}%");
            }

            if ($request->filled('country')) {
                $query->where('country', 'like', "%{$request->country}%");
            }

            if ($request->filled('min_popularity')) {
                $query->where('popularity_score', '>=', $request->min_popularity);
            }

            if ($request->filled('max_popularity')) {
                $query->where('popularity_score', '<=', $request->max_popularity);
            }

            if ($request->filled('date_from')) {
                $query->where('feast_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('feast_date', '<=', $request->date_to);
            }

            // Solo santos activos
            $query->where('is_active', true);

            // Ordenamiento
            $query->orderBy('popularity_score', 'desc')
                  ->orderBy('feast_date', 'asc');

            // Paginación
            $perPage = $request->get('per_page', 15);
            $saints = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Búsqueda de santos realizada exitosamente',
                'data' => $saints->items(),
                'filters_applied' => $request->only(['q', 'category', 'feast_type', 'liturgical_color', 'is_patron', 'region', 'country', 'min_popularity', 'max_popularity', 'date_from', 'date_to']),
                'links' => [
                    'first' => $saints->url(1),
                    'last' => $saints->url($saints->lastPage()),
                    'prev' => $saints->previousPageUrl(),
                    'next' => $saints->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $saints->currentPage(),
                    'last_page' => $saints->lastPage(),
                    'per_page' => $saints->perPage(),
                    'total' => $saints->total(),
                    'from' => $saints->firstItem(),
                    'to' => $saints->lastItem(),
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            \Log::error('Error en CatholicSaintController@search: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtiene estadísticas del santoral
     * 
     * @return JsonResponse
     * 
     * @api {get} /api/catholic-saints/stats Estadísticas del santoral
     * @apiName GetSaintStats
     * @apiGroup CatholicSaints
     * @apiVersion 1.0.0
     */
    public function stats(): JsonResponse
    {
        try {
            // Usar caché para estadísticas
            $stats = Cache::remember('catholic_saints_stats', now()->addHours(24), function () {
                $totalSaints = CatholicSaint::count();
                $activeSaints = CatholicSaint::where('is_active', true)->count();
                $universalSaints = CatholicSaint::where('is_universal', true)->count();
                $localSaints = CatholicSaint::where('is_local', true)->count();
                $patronSaints = CatholicSaint::where('is_patron', true)->count();

                // Estadísticas por categoría
                $categoryStats = CatholicSaint::selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->orderBy('count', 'desc')
                    ->get();

                // Estadísticas por tipo de celebración
                $feastTypeStats = CatholicSaint::selectRaw('feast_type, COUNT(*) as count')
                    ->groupBy('feast_type')
                    ->orderBy('count', 'desc')
                    ->get();

                // Santos más populares
                $topSaints = CatholicSaint::select('id', 'name', 'popularity_score', 'category')
                    ->where('is_active', true)
                    ->orderBy('popularity_score', 'desc')
                    ->limit(10)
                    ->get();

                // Próximas celebraciones (próximos 7 días)
                $upcomingFeasts = CatholicSaint::where('is_active', true)
                    ->where('feast_date', '>=', now())
                    ->where('feast_date', '<=', now()->addDays(7))
                    ->orderBy('feast_date', 'asc')
                    ->select('id', 'name', 'feast_date', 'category', 'feast_type')
                    ->get();

                return [
                    'total_saints' => $totalSaints,
                    'active_saints' => $activeSaints,
                    'universal_saints' => $universalSaints,
                    'local_saints' => $localSaints,
                    'patron_saints' => $patronSaints,
                    'category_stats' => $categoryStats,
                    'feast_type_stats' => $feastTypeStats,
                    'top_saints' => $topSaints,
                    'upcoming_feasts' => $upcomingFeasts,
                    'generated_at' => now()->toISOString()
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas del santoral obtenidas exitosamente',
                'data' => $stats
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            \Log::error('Error en CatholicSaintController@stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
