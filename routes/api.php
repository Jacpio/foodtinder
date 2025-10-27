<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SwipeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/get-the-most-popular-parameter', [HomeController::class, 'getParameterSwipe']);
    Route::get('/swipe-cards', [SwipeController::class, 'swipeCards']);
    Route::get('/swipe-cards/{id}', [SwipeController::class, 'swipeCardsByParameter'])->name('swipe-cards-by-parameter');
    Route::post('/swipe-decision', [SwipeController::class, 'swipeDecision']);
    Route::get('/recommended-dishes', [RecommendationController::class, 'recommendation']);
});

Route::get('/share/recommendations', [RecommendationController::class, 'shareRecommendations']);

require __DIR__.'/auth.php';
require __DIR__.'/admin_api.php';
