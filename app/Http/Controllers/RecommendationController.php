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
     *   summary="User recommendations",
     *   description="Returns a paginated list of recommended dishes for the authenticated user. Each item includes a `match_score` computed from user's parameter weights.",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(
     *     name="per_page", in="query", required=false,
     *     description="Items per page (1–10, default 10)",
     *     @OA\Schema(type="integer", minimum=1, maximum=10, default=10)
     *   ),
     *   @OA\Parameter(
     *     name="page", in="query", required=false,
     *     description="Page number (>= 1, default 1)",
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
     *           @OA\Property(property="is_vegan", type="boolean", example=false),
     *           @OA\Property(
     *             property="parameters",
     *             type="array",
     *             @OA\Items(
     *               type="object",
     *               @OA\Property(property="id", type="integer", example=5),
     *               @OA\Property(property="name", type="string", example="Włoska"),
     *               @OA\Property(
     *                 property="type",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="cuisine")
     *               )
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
     *       @OA\Property(property="prev_page_url", type="string", nullable=true),
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

        $paginator = $this->recommendationService->recommendedDishes($request->user(), $perPage);

        return response()->json($paginator);
    }

    /**
     * @OA\Get(
     *   path="/api/share/recommendations",
     *   tags={"Recommendations"},
     *   summary="Fetch dishes by IDs (public)",
     *   description="Returns a list of dishes (with parameters and their types) for sharing. No authentication required.",
     *   @OA\Parameter(
     *     name="ids",
     *     in="query",
     *     required=true,
     *     description="Comma-separated dish IDs",
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
     *         @OA\Property(property="is_vegan", type="boolean", example=false),
     *         @OA\Property(
     *           property="parameters",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=12),
     *             @OA\Property(property="name", type="string", example="Włoska"),
     *             @OA\Property(
     *               property="type",
     *               type="object",
     *               @OA\Property(property="id", type="integer", example=2),
     *               @OA\Property(property="name", type="string", example="cuisine")
     *             )
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
    public function shareRecommendations(Request $request): JsonResponse
    {
        $idsRaw = (string) $request->query('ids', '');
        $ids = collect(explode(',', $idsRaw))
            ->map(fn ($v) => trim($v))
            ->filter(fn ($v) => $v !== '' && ctype_digit($v))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return response()->json([]);
        }

        $dishes = Dish::with('parameters')->whereIn('id', $ids)->get();

        return response()->json($dishes->values());
    }
}
