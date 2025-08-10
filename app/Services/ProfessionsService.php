<?php

namespace App\Services;

use App\Models\Profession;
use Illuminate\Database\Eloquent\Collection;

class ProfessionsService
{
    public function list(): Collection
    {
        return Profession::all();
    }

    public function findByIdOrSlug(string $idOrSlug): Profession
    {
        return Profession::where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();
    }

    public function create(array $validatedData): Profession
    {
        return Profession::create($validatedData);
    }
}


