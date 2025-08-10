<?php

namespace App\Services;

use App\Models\Award;
use Illuminate\Database\Eloquent\Collection;

class AwardsService
{
    public function listAwards(): Collection
    {
        return Award::with('awardWinners')->get();
    }

    public function getAwardByIdOrSlug(string $idOrSlug): Award
    {
        return Award::with('awardWinners')
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();
    }

    public function createAward(array $validatedData): Award
    {
        return Award::create($validatedData);
    }
}


