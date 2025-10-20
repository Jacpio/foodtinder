<?php

use App\Http\Controllers\DishController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/dish', [DishController::class, 'index']);
    Route::post('/dish/{id}', [DishController::class, 'destroy']);
    Route::put('/dish/{id}', [DishController::class, 'update']);
    Route::delete('/dish/{id}', [DishController::class, 'destroy']);
});
