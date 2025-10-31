<?php
use App\Http\Controllers\DishController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\TypeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::prefix('dish')->group(function () {
        Route::post('import-csv', [DishController::class, 'importCSV'])->name('dish.import-csv');;
        Route::get('/', [DishController::class, 'index']);
        Route::get('{id}', [DishController::class, 'show'])->whereNumber('id');
        Route::post('/', [DishController::class, 'store']);
        Route::put('{id}', [DishController::class, 'update'])->whereNumber('id');
        Route::delete('{id}', [DishController::class, 'destroy'])->whereNumber('id');
    });

    Route::prefix('parameter')->group(function () {
        Route::post('import-csv', [ParameterController::class, 'importCSV']);
        Route::get('/', [ParameterController::class, 'index']);
        Route::get('{id}', [ParameterController::class, 'show'])->whereNumber('id');
        Route::post('/', [ParameterController::class, 'store']);
        Route::put('{id}', [ParameterController::class, 'update'])->whereNumber('id');
        Route::delete('{id}', [ParameterController::class, 'destroy'])->whereNumber('id');
    });
    Route::prefix('types')->group(function () {
        Route::get('/', [TypeController::class, 'index']);
        Route::get('{id}', [TypeController::class, 'show'])->whereNumber('id');
        Route::post('/', [TypeController::class, 'store']);
        Route::put('{id}', [TypeController::class, 'update'])->whereNumber('id');
        Route::delete('{id}', [TypeController::class, 'destroy'])->whereNumber('id');
    });
});
