<?php

namespace App\Services;

use App\Models\AwardWinner;
use Illuminate\Database\Eloquent\Collection;

class AwardWinnersService
{
    public function listWinners(): Collection
    {
        return AwardWinner::with(['person', 'award', 'work', 'municipality'])->get();
    }

    public function getWinnerById(int $id): AwardWinner
    {
        return AwardWinner::with(['person', 'award', 'work', 'municipality'])->findOrFail($id);
    }

    public function createWinner(array $validatedData): AwardWinner
    {
        return AwardWinner::create($validatedData);
    }
}


