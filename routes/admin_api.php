<?php

use App\Http\Controllers\DishController;
use App\Http\Controllers\ParameterController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::prefix('dish')->group(function () {
        Route::get('/', [DishController::class, 'index']);
        Route::get('/{id}', [DishController::class, 'show']);
        Route::post('/{id}', [DishController::class, 'destroy']);
        Route::put('/{id}', [DishController::class, 'update']);
        Route::delete('/{id}', [DishController::class, 'destroy']);
    });
    Route::prefix('parameter')->group(function () {
        Route::get('/', [ParameterController::class, 'index']);
        Route::get('/{id}', [ParameterController::class, 'show']);
        Route::post('/{id}', [ParameterController::class, 'destroy']);
        Route::put('/{id}', [ParameterController::class, 'update']);
        Route::delete('/{id}', [ParameterController::class, 'destroy']);
        Route::delete('/import-csv', [ParameterController::class, 'importCSV']);
    });

});
