<?php

use App\Models\Image;
use App\Services\ImagesService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates and updates an image', function () {
    $service = new ImagesService();
    $image = $service->create([
        'url' => 'https://example.com/image.jpg',
    ]);

    $service->update($image, ['alt_text' => 'Alt']);

    expect($image->fresh()->alt_text)->toBe('Alt');
});


