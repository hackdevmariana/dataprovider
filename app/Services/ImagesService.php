<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ImagesService
{
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return Image::paginate($perPage);
    }

    public function findById(int $id): Image
    {
        return Image::findOrFail($id);
    }

    public function create(array $validatedData): Image
    {
        return Image::create($validatedData);
    }

    public function update(Image $image, array $validatedData): Image
    {
        $image->update($validatedData);
        return $image;
    }

    public function delete(Image $image): void
    {
        $image->delete();
    }
}


