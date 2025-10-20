<?php

namespace App\Http\Controllers;

use AllowDynamicProperties;
use App\Services\RecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use OpenApi\Annotations as OA;

#[AllowDynamicProperties] class RecommendationController extends Controller
{

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }
    /**
     * @OA\Get(
     *   path="/api/recommended-dishes",
     *   operationId="recommendation",
     *   tags={"Recommendations"},
     *   summary="Rekomendacje dla użytkowników",
     *   description="Zwraca listę rekomendowanych dań dla aktualnego użytkownika.",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(
     *     name="limit", in="query", required=false,
     *     description="Maksymalna liczba pozycji",
     *     @OA\Schema(type="integer", minimum=1, maximum=20, default="")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/DishWithScore"),
     *       example={
     *         {"id":101,"name":"Spaghetti Carbonara","category_id":1,"cuisine_id":3,"flavour_id":5,"image_url":"spaghetti.jpg","image_url_full":"http://localhost/storage/spaghetti.jpg","description":"Classic","match_score":0.93}
     *       }
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */

    public function recommendation(Request $request): JsonResponse{
        $data = $request->validate([
            'limit' => 'nullable|integer|min:1',
        ]);
        $user = Auth::user();
        $limit = $data['limit'] ?? null;
        $dishes = $this->recommendationService->recommendedDishes($user, $limit);
        return response()->json($dishes);
    }
}
