<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum, ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value])
    ->group(function () {
        Route::get('/auth/refresh', [AuthController::class, 'refreshToken']);
});
