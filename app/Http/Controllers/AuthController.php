<?php

namespace App\Http\Controllers;

use App\Enums\TokenAbility;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $name = $request->post('name');
        $email = $request->post('email');
        $password = $request->post('password');

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ];

        try {
            $user = User::create($data);
            $accessTokenExpiration = Carbon::now()->addMinutes(config('sanctum.ac_expiration'));
            $refreshTokenExpiration = Carbon::now()->addMinutes(config('sanctum.rt_expiration'));
            $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], $accessTokenExpiration);
            $refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], $refreshTokenExpiration);

            return [
                'accessToken' => $accessToken->plainTextToken,
                'accessTokenExpiration' => $accessTokenExpiration->toIso8601String(),
                'refreshToken' => $refreshToken->plainTextToken,
                'refreshTokenExpiration' => $refreshTokenExpiration->toIso8601String(),
            ];
        } catch (UniqueConstraintViolationException) {
            return response(['message' => 'Email already exists.'], 409);
        }
    }

    public function login(Request $request)
    {
        $email = $request->post('email');
        $password = $request->post('password');

        if (!auth()->attempt(['email' => $email, 'password' => $password])) {
            return response(['message' => 'Invalid credentials'], 401);
        }

        $accessTokenExpiration = Carbon::now()->addMinutes(config('sanctum.ac_expiration'));
        $refreshTokenExpiration = Carbon::now()->addMinutes(config('sanctum.rt_expiration'));
        $accessToken = auth()->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], $accessTokenExpiration);
        $refreshToken = auth()->user()->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], $refreshTokenExpiration);

        return [
            'accessToken' => $accessToken->plainTextToken,
            'accessTokenExpiration' => $accessTokenExpiration->toIso8601String(),
            'refreshToken' => $refreshToken->plainTextToken,
            'refreshTokenExpiration' => $refreshTokenExpiration->toIso8601String(),
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response(['message' => 'Successfully logged out'], 200);
    }

    public function refreshToken(Request $request)
    {
        $accessTokenExpiration = Carbon::now()->addMinutes(config('sanctum.ac_expiration'));
        $accessToken = $request->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], $accessTokenExpiration);
        return response([
            'message' => 'Token generated',
            'accessToken' => $accessToken->plainTextToken,
            'accessTokenExpiration' => $accessTokenExpiration->toIso8601String(),
        ]);
    }
}
