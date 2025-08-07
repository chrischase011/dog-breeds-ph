<?php

namespace App\Helpers;

use Barryvdh\Debugbar\Facades\Debugbar;
use \Illuminate\Support\Facades\Http;

class Dog
{
protected static ?string $baseUrl = null;

  protected static function baseUrl(): string
  {
      return self::$baseUrl ??= config('app.dog_api');
  }

  public static function fetchBreedImage(string $breed): ?string
  {
    $url = self::baseUrl() . "breed/{$breed}/images/random";
    $response = Http::get($url);

    if ($response->successful() && $response->json('status') === 'success') {
      return $response->json('message');
    }

    return null;
  }

  public static function getAllBreeds(): array
  {
    $url = self::baseUrl() . "breeds/list/all";
    Debugbar::info($url);
    $response = Http::get($url);

    if (!$response->successful()) {
      Debugbar::info('Failed to fetch breeds');
      return [];
    }

    $breeds = $response->json('message');
    $breedList = [];

    foreach ($breeds as $breed => $subBreeds) {
      $image = self::fetchBreedImage($breed);
      $breedList[] = [
        'name' => ucfirst($breed),
        'image' => $image,
      ];
    }

    return $breedList;
  }
}