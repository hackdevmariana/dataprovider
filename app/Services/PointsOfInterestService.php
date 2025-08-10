<?php

namespace App\Services;

use App\Models\PointOfInterest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PointsOfInterestService
{
    public function paginate(int $perPage = 50): LengthAwarePaginator
    {
        return PointOfInterest::with(['municipality', 'tags'])->paginate($perPage);
    }

    public function findByIdOrSlug(string $idOrSlug): ?PointOfInterest
    {
        return PointOfInterest::with(['municipality', 'tags'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->first();
    }

    public function create(array $validatedData): PointOfInterest
    {
        return PointOfInterest::create($validatedData);
    }

    public function update(PointOfInterest $poi, array $validatedData): PointOfInterest
    {
        $poi->update($validatedData);
        return $poi;
    }
}


