<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Services\SwipeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class SwipeController extends Controller
{
    public function __construct(private SwipeService $swipeService)
    {
    }

    /**
     * @OA\Get(
     *   path="/api/swipe-cards",
     *   tags={"Swipes"},
     *   summary="Pobierz karty do swipe’owania (nieswipe’owane dania z parametrami)",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(
     *     name="limit", in="query", required=false,
     *     description="Ile kart zwrócić (1–10, domyślnie 5)",
     *     @OA\Schema(type="integer", minimum=1, maximum=10, default=5)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Lista dań",
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
     *             @OA\Property(property="name", type="string", example="cuisine:Włoska"),
     *             @OA\Property(property="type", type="string", example="enum"),
     *             @OA\Property(property="value", type="number", format="float", example=1),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *           )
     *         ),
     *         @OA\Property(property="created_at", type="string", format="date-time"),
     *         @OA\Property(property="updated_at", type="string", format="date-time")
     *       )
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function swipeCards(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => 'sometimes|integer|min:1|max:10',
        ]);

        $user = $request->user();
        $limit = $validated['limit'] ?? 5;

        $dishes = $this->swipeService->getUnswipedDishes($user, $limit);

        return response()->json($dishes);
    }

    /**
     * @OA\Post(
     *   path="/api/swipe-decision",
     *   tags={"Swipes"},
     *   summary="Zapisz decyzję swipe (like/dislike) i zaktualizuj wagi parametrów",
     *   security={{"bearerAuth": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"dish_id","decision"},
     *       @OA\Property(property="dish_id", type="integer", example=1),
     *       @OA\Property(property="decision", type="string", enum={"like","dislike"}, example="like")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Zapisano",
     *     @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="success"))
     *   ),
     *   @OA\Response(response=404, description="Dish not found"),
     *   @OA\Response(
     *     response=422,
     *     description="Błąd walidacji",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="The given data was invalid."),
     *       @OA\Property(
     *         property="errors",
     *         type="object",
     *         example={"dish_id":{"The selected dish id is invalid."}}
     *       )
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function swipeDecision(Request $request): JsonResponse
    {
        $data = $request->validate([
            'dish_id'  => 'required|integer|exists:dishes,id',
            'decision' => 'required|string|in:like,dislike',
        ]);

        $user = $request->user();

        $dish = Dish::find($data['dish_id']);
        if (!$dish) {
            return response()->json(['message' => 'Dish not found'], 404);
        }

        $this->swipeService->swipe($user, $dish, $data['decision']);

        return response()->json(['message' => 'success'], 200);
    }
}
