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
     *     @OA\Schema(type="integer", minimum=1, maximum=20, default=10)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=101),
     *         @OA\Property(property="name", type="string", example="Spaghetti Carbonara"),
     *         @OA\Property(property="description", type="string", nullable=true, example="Classic"),
     *         @OA\Property(property="image_url", type="string", nullable=true, example="spaghetti.jpg"),
     *         @OA\Property(
     *           property="parameters",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=5),
     *             @OA\Property(property="name", type="string", example="Włoska"),
     *             @OA\Property(property="type", type="string", enum={"category","cuisine","flavour","other"}, example="cuisine")
     *           )
     *         ),
     *         @OA\Property(property="match_score", type="number", format="float", example=3.0)
     *       )
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
