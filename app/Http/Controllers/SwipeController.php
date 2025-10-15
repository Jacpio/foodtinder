<?php

namespace App\Http\Controllers;

use AllowDynamicProperties;
use App\Models\Dish;
use App\Services\SwipeService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

#[AllowDynamicProperties] class SwipeController extends Controller
{
    public function __construct(SwipeService $dishService)
    {
        $this->dishService = $dishService;
    }

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
