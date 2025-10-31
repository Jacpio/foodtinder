<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\User;
use App\Services\SwipeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class SwipeController extends Controller
{
    public function __construct(private readonly SwipeService $swipeService) {}

    /**
     * @OA\Get(
     *   path="/api/swipe-cards",
     *   tags={"Swipes"},
     *   summary="Get swipeable cards (unswiped dishes with attached parameters)",
     *   description="Returns a random list of the current user's unswiped dishes together with their parameters.",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(
     *     name="limit", in="query", required=false,
     *     description="How many cards to return (1–10, default 5)",
     *     @OA\Schema(type="integer", minimum=1, maximum=10, default=5)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="List of dishes",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Spaghetti Bolognese"),
     *         @OA\Property(property="description", type="string", nullable=true, example="Classic beef & tomato ragù."),
     *         @OA\Property(property="image_url", type="string", nullable=true, example="spaghetti.jpg"),
     *         @OA\Property(property="is_vegan", type="boolean", example=false),
     *         @OA\Property(
     *           property="parameters",
     *           type="array",
     *           description="Dish parameters (many-to-many).",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=12),
     *             @OA\Property(property="name", type="string", example="Italian"),
     *             @OA\Property(
     *               property="type",
     *               type="object",
     *               @OA\Property(property="id", type="integer", example=2),
     *               @OA\Property(property="name", type="string", example="cuisine")
     *             ),
     *             @OA\Property(property="value", type="number", format="float", example=1),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *           )
     *         ),
     *         @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-08T12:34:56Z"),
     *         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-10-08T12:34:56Z")
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

        /** @var User $user */
        $user  = $request->user();
        $limit = $validated['limit'] ?? 5;

        $dishes = $this->swipeService->getUnswipedDishes($user, $limit);

        return response()->json($dishes);
    }

    /**
     * @OA\Get(
     *   path="/api/swipe-cards/{id}",
     *   tags={"Swipes"},
     *   summary="Get swipeable cards filtered by a parameter",
     *   description="Returns a random list of the current user's unswiped dishes that contain the given parameter.",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(
     *     name="id", in="path", required=true,
     *     description="Parameter ID",
     *     @OA\Schema(type="integer", minimum=1)
     *   ),
     *   @OA\Parameter(
     *     name="limit", in="query", required=false,
     *     description="How many cards to return (1–10, default 5)",
     *     @OA\Schema(type="integer", minimum=1, maximum=10, default=5)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="List of dishes",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Vegan Pad Thai"),
     *         @OA\Property(property="description", type="string", nullable=true),
     *         @OA\Property(property="image_url", type="string", nullable=true),
     *         @OA\Property(property="is_vegan", type="boolean", example=true),
     *         @OA\Property(
     *           property="parameters",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=44),
     *             @OA\Property(property="name", type="string", example="Thai"),
     *             @OA\Property(
     *               property="type",
     *               type="object",
     *               @OA\Property(property="id", type="integer", example=2),
     *               @OA\Property(property="name", type="string", example="cuisine")
     *             ),
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
    public function swipeCardsByParameter(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'limit' => 'sometimes|integer|min:1|max:10',
        ]);

        \Validator::validate(['id' => $id], [
            'id' => 'required|integer|exists:parameters,id',
        ]);

        /** @var User $user */
        $user  = $request->user();
        $limit = $validated['limit'] ?? 5;

        $dishes = $this->swipeService->getUnswipedDishesByParameter($user, $limit, $id);

        return response()->json($dishes);
    }

    /**
     * @OA\Post(
     *   path="/api/swipe-decision",
     *   tags={"Swipes"},
     *   summary="Save swipe decision (like/dislike) and update parameter weights",
     *   description="Updates the user's ParameterWeight for all parameters attached to the dish. Also records the swipe.",
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
     *     description="Saved",
     *     @OA\JsonContent(type="object",
     *       @OA\Property(property="message", type="string", example="success")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Dish not found",
     *     @OA\JsonContent(type="object",
     *       @OA\Property(property="message", type="string", example="Dish not found")
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error",
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

        /** @var User $user */
        $user = $request->user();

        $dish = Dish::find($data['dish_id']);
        if (!$dish) {
            return response()->json(['message' => 'Dish not found'], 404);
        }

        $this->swipeService->swipe($user, $dish, $data['decision']);

        return response()->json(['message' => 'success'], 200);
    }
}
