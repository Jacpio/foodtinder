<?php

use App\Http\Controllers\DishController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/swipe-cards', [DishController::class, 'swipeCards']);
    Route::post('/swipe-decision', [DishController::class, 'swipeDecision']);
    Route::get('/recommended-dishes', [DishController::class, 'recommendation']);
});
