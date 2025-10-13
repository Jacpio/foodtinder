<?php

namespace App\Http\Controllers;

use AllowDynamicProperties;
use App\Models\Dish;
use App\Service\DishService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

#[AllowDynamicProperties] class DishController extends Controller
{
    public function __construct(DishService $dishService)
    {
        $this->dishService = $dishService;
    }

    public function swipeCards(): JsonResponse
    {
        $user = Auth::user();

        $swipedDishIds = $user->swipes()->pluck('dish_id');
        $dishes = Dish::with(['category', 'cuisine', 'flavour'])
            ->whereNotIn('id', $swipedDishIds)
            ->inRandomOrder()
            ->take(5)
            ->get();

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

        return response()->json([], 204);
    }

    public function recommendation(Request $request): JsonResponse{
        $user = Auth::user();
        $dish =  $this->dishService->recommendedDishes($user);
        return response()->json($dish);
    }
}
