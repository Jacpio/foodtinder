<?php

use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SwipeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/swipe-cards', [SwipeController::class, 'swipeCards']);
    Route::post('/swipe-decision', [SwipeController::class, 'swipeDecision']);
    Route::get('/recommended-dishes', [RecommendationController::class, 'recommendation']);
});

require __DIR__.'/auth.php';
