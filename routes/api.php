<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\FilmController;

// ENDPOINTS DE AUTENTICACIÓN (Agrupados bajo el prefijo 'auth')
Route::group(['prefix' => 'auth'], function () {
    
    // Login público (No requiere token)
    Route::post('login', [AuthController::class, 'login']);

    // Logout, Refresh y Me protegidos por JWT (Requieren token)
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// ENDPOINTS DEL CRUD (Protegidos por JWT)
Route::middleware('auth:api')->group(function () {
    Route::apiResource('directores', DirectorController::class)->names('directors');
    Route::apiResource('peliculas', App\Http\Controllers\FilmController::class);
});