<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\YandexReviewsParser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function parseYandexReviews(Request $request, YandexReviewsParser $parser): JsonResponse
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $url = $request->input('url');
        
        if (!str_contains($url, 'yandex.ru/maps')) {
            return response()->json([
                'success' => false,
                'message' => 'Некорректная ссылка на Яндекс.Карты'
            ], 400);
        }
        $reviews = $parser->parseReviews($url);
        
        return response()->json($reviews);
    }
}