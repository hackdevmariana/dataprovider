<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CalendarHoliday;
use App\Http\Resources\V1\CalendarHolidayResource;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="CalendarHolidays",
 *     description="API for managing calendar holidays (festivos)"
 * )
 */
class CalendarHolidayController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/calendar-holidays",
     *     summary="Get all calendar holidays",
     *     tags={"CalendarHolidays"},
     *     @OA\Response(
     *         response=200,
     *         description="List of calendar holidays",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CalendarHoliday"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return CalendarHolidayResource::collection(CalendarHoliday::all());
    }

    /**
     * @OA\Get(
     *     path="/api/v1/calendar-holidays/{idOrSlug}",
     *     summary="Get calendar holiday by ID or slug",
     *     tags={"CalendarHolidays"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID or slug of the holiday",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Holiday found",
     *         @OA\JsonContent(ref="#/components/schemas/CalendarHoliday")
     *     ),
     *     @OA\Response(response=404, description="Holiday not found")
     * )
     */
    public function show($idOrSlug)
    {
        $holiday = CalendarHoliday::where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();
        return new CalendarHolidayResource($holiday);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/calendar-holidays/date/{date}",
     *     summary="Get holidays by date (YYYY-MM-DD)",
     *     tags={"CalendarHolidays"},
     *     @OA\Parameter(
     *         name="date",
     *         in="path",
     *         required=true,
     *         description="Date in YYYY-MM-DD format",
     *         @OA\Schema(type="string", example="2024-12-25")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Holidays for the given date",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CalendarHoliday"))
     *         )
     *     )
     * )
     */
    public function byDate($date)
    {
        $holidays = CalendarHoliday::where('date', $date)->get();
        return CalendarHolidayResource::collection($holidays);
    }
}
