<?php

namespace App\Http\Controllers;

use AllowDynamicProperties;
use App\Models\Dish;
use App\Services\SwipeService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

#[AllowDynamicProperties] class SwipeController extends Controller
{
    public function __construct(SwipeService $dishService)
    {
        $this->dishService = $dishService;
    }
    /**
     * @OA\Get(
     *   path="/api/swipe-cards",
     *   tags={"Swipes"},
     *   summary="Pobierz karty do swipe’owania",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(
     *     name="limit", in="query", required=false,
     *     @OA\Schema(type="integer", minimum=1, maximum=10, default=5)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Dish"))
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */

    public function swipeCards(Request $request): JsonResponse
    {
        $user = Auth::user();
        $request->validate([
                'limit' => 'nullable|int|min:1|max:10',
            ]);

        $limit = $validated['limit'] ?? 5;
        $dishes = $this->dishService->getUnswipedDishes($user, $limit);

        return response()->json($dishes);
    }
    /**
     * @OA\Post(
     *   path="/api/swipe-decision",
     *   tags={"Swipes"},
     *   summary="Zapisz decyzję swipe (like/dislike)",
     *   security={{"bearerAuth": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/SwipeDecisionRequest")
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="object", example={})),
     *   @OA\Response(response=404, description="Dish not found"),
     *   @OA\Response(
     *     response=422,
     *     description="Błąd walidacji",
     *     @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function swipeDecision(Request $request): JsonResponse|Response
    {
        $data = $request->validate([
            'dish_id' => 'required|integer|exists:dishes,id',
            'decision' => 'required|in:like,dislike'
        ]);
        $dish = Dish::find($data['dish_id']);
        if (!$dish){
            return response()->json(['message' => 'Dish not found'], 404);
        }
        $userId = Auth::id();
        $this->dishService->addWeightToCategory($dish->category_id, $userId, $data['decision']);
        $this->dishService->addWeightToFlavour($dish->flavour_id, $userId, $data['decision']);
        $this->dishService->addWeightToCuisine($dish->cuisine_id, $userId, $data['decision']);

        return response()->json([], 200);
    }
}
