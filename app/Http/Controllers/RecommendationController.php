<?php

namespace App\Http\Controllers;

use AllowDynamicProperties;
use App\Services\RecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

#[AllowDynamicProperties] class RecommendationController extends Controller
{

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function recommendation(): JsonResponse{
        $user = Auth::user();
        $dish =  $this->recommendationService->recommendedDishes($user);
        return response()->json($dish);
    }
}
