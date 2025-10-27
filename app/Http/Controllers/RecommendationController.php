<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Services\RecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class RecommendationController extends Controller
{
    public function __construct(private readonly RecommendationService $recommendationService)
    {
    }

    /**
     * @OA\Get(
     *   path="/api/recommended-dishes",
     *   operationId="recommendation",
     *   tags={"Recommendations"},
     *   summary="Rekomendacje dla użytkownika",
     *   description="Zwraca paginowaną listę rekomendowanych dań z polem `match_score`.",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(
     *     name="per_page", in="query", required=false,
     *     description="Ilość na stronę (1–10, domyślnie 10)",
     *     @OA\Schema(type="integer", minimum=1, maximum=10, default=10)
     *   ),
     *   @OA\Parameter(
     *     name="page", in="query", required=false,
     *     description="Numer strony (>=1, domyślnie 1)",
     *     @OA\Schema(type="integer", minimum=1, default=1)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK (LengthAwarePaginator)",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="current_page", type="integer", example=1),
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="id", type="integer", example=101),
     *           @OA\Property(property="name", type="string", example="Spaghetti Carbonara"),
     *           @OA\Property(property="description", type="string", nullable=true, example="Classic"),
     *           @OA\Property(property="image_url", type="string", nullable=true, example="spaghetti.jpg"),
     *           @OA\Property(
     *             property="parameters",
     *             type="array",
     *             @OA\Items(
     *               type="object",
     *               @OA\Property(property="id", type="integer", example=5),
     *               @OA\Property(property="name", type="string", example="Włoska"),
     *               @OA\Property(property="type", type="string", enum={"category","cuisine","flavour","other"}, example="cuisine")
     *             )
     *           ),
     *           @OA\Property(property="match_score", type="number", format="float", example=6)
     *         )
     *       ),
     *       @OA\Property(property="first_page_url", type="string", example="http://localhost/api/recommended-dishes?page=1"),
     *       @OA\Property(property="from", type="integer", nullable=true, example=1),
     *       @OA\Property(property="last_page", type="integer", example=5),
     *       @OA\Property(property="last_page_url", type="string", example="http://localhost/api/recommended-dishes?page=5"),
     *       @OA\Property(
     *         property="links",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="url", type="string", nullable=true),
     *           @OA\Property(property="label", type="string"),
     *           @OA\Property(property="active", type="boolean")
     *         )
     *       ),
     *       @OA\Property(property="next_page_url", type="string", nullable=true, example="http://localhost/api/recommended-dishes?page=2"),
     *       @OA\Property(property="path", type="string", example="http://localhost/api/recommended-dishes"),
     *       @OA\Property(property="per_page", type="integer", example=10),
     *       @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *       @OA\Property(property="to", type="integer", nullable=true, example=10),
     *       @OA\Property(property="total", type="integer", example=47)
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function recommendation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:10',
            'page'     => 'sometimes|integer|min:1',
        ]);

        $perPage = (int) ($validated['per_page'] ?? 10);

        $page = (int) ($validated['page'] ?? 1);
        $paginator = $this->recommendationService->recommendedDishes($request->user(), $perPage, $page);

        return response()->json($paginator);
    }
    /**
     * @OA\Get(
     *   path="/api/share/recommendations",
     *   tags={"Recommendations"},
     *   summary="Pobierz dania przez id",
     *   description="Zwraca listę dań, by udostępić ją.",
     *   @OA\Parameter(
     *     name="ids",
     *     in="query",
     *     required=true,
     *     description="Oddzielone przecinkiem id dań",
     *     @OA\Schema(type="string", pattern="^\\d+(,\\d+)*$", example="1,5,9")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Spaghetti Bolognese"),
     *         @OA\Property(property="description", type="string", nullable=true),
     *         @OA\Property(property="image_url", type="string", nullable=true, example="spaghetti.jpg"),
     *         @OA\Property(
     *           property="parameters",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=12),
     *             @OA\Property(property="name", type="string", example="Włoska"),
     *             @OA\Property(property="type", type="string", enum={"category","cuisine","flavour","other"}, example="cuisine"),
     *             @OA\Property(property="value", type="number", format="float", example=1),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *           )
     *         ),
     *         @OA\Property(property="created_at", type="string", format="date-time"),
     *         @OA\Property(property="updated_at", type="string", format="date-time")
     *       )
     *     )
     *   ),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function shareRecommendations(Request $request): JsonResponse{
        $ids = $request->query('ids');
        $ids = explode(',', $ids);
        if (is_array($ids)) {
            $dishes = Dish::with('parameters')->whereIn('id', $ids)->get();
            return response()->json($dishes);
        }
        return response()->json(['message' => 'Not Found'], 404);
    }
}
