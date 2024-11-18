<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WidgetsController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::get('/login', function () {
        return response(['message' => 'You are no longer authenticated. Please login.'], 401);
    })->name('login');

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/password/forgot', [PasswordResetController::class, 'forgot']);
    Route::post('/password/reset', [PasswordResetController::class, 'reset']);

    Route::middleware(['auth:sanctum'])->group(function() {
        Route::get('/user', [UserController::class, 'view']);
        Route::post('/user', [UserController::class, 'update']);
        Route::delete('/user', [UserController::class, 'destroy']);

        Route::get('/logout/', [AuthController::class, 'logout']);

        Route::middleware('ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value)
            ->group(function () {
                Route::get('/refresh', [AuthController::class, 'refreshToken']);
        });
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('widgets', [WidgetsController::class, 'list']);
    Route::post('widgets', [WidgetsController::class, 'store']);
    Route::get('widgets/{id}', [WidgetsController::class, 'view']);
    Route::post('widgets/{id}', [WidgetsController::class, 'update']);
    Route::delete('widgets/{id}', [WidgetsController::class, 'destroy']);
});
