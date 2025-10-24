<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class HomeController extends Controller
{

    public function __construct(private readonly RecommendationService $recommendationService){}
    /**
     * @OA\Get(
     *   path="/api/get-the-most-popular-parameters",
     *   operationId="getMostPopularParametersLinks",
     *   tags={"Swipes"},
     *   summary="Linki do najczęściej lubianych parametrów użytkownika",
     *   description="Zwraca tablicę URL-i do endpointu zwracającego karty po parametrze.",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(
     *     name="limit", in="query", required=false,
     *     description="Ile linków zwrócić (1–50, domyślnie 6)",
     *     @OA\Schema(type="integer", minimum=1, maximum=50, default=6)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(type="string", format="uri", example="http://127.0.0.1:8000/api/swipe-cards-by-parameter/14")
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getParameterSwipe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'limit' => 'integer|min:1|max:12|nullable',
        ]);

        $limit = $data['limit'] ?? 6;
        $user = $request->user();
        $parameters = $this->recommendationService->getUserTopParameters($user, $limit);

        $links = $parameters->map(fn ($p) =>
            route('swipe-cards-by-parameter', ['id' => $p->id])
        )->values()->all();
        return response()->json($links);
    }
}
