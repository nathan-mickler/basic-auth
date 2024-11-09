<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;
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
        Route::get('/user/', function (Request $request) {
            return $request->user();
        });

        Route::get('/logout/', [AuthController::class, 'logout']);

        Route::middleware('ability:' . TokenAbility::ACCESS_API->value)
            ->group(function () {
                Route::get('/refresh', [AuthController::class, 'refreshToken']);
        });
    });
});
