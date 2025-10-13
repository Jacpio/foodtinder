<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->noContent();
});

require __DIR__.'/auth.php';
