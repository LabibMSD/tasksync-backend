<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->name('register');
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout')
        ->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResources([
        '/users' => UserController::class,
    ]);
});
