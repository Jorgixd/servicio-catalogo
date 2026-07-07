<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogoService\CatalogoController;

Route::prefix('catalogo')->group(function () {
    Route::get('/', [CatalogoController::class, 'index']);
    Route::post('/', [CatalogoController::class, 'store']);
    Route::get('/buscar', [CatalogoController::class, 'buscar']);
    Route::put('/{id}', [CatalogoController::class, 'update']);
    Route::delete('/{id}', [CatalogoController::class, 'destroy']);
});