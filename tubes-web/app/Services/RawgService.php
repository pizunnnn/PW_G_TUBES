<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RawgService
{
    public function getPopularGames()
    {
        $response = Http::get('https://api.rawg.io/api/games', [
            'key' => env('RAWG_API_KEY'),
            'page_size' => 6,
        ]);

        return $response->successful()
            ? $response->json()['results']
            : [];
    }
}
