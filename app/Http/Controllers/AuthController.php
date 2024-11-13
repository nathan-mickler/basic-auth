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
            $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
            $refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));

            return [
                'accessToken' => $accessToken->plainTextToken,
                'refreshToken' => $refreshToken->plainTextToken,
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

        $accessToken = auth()->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
        $refreshToken = auth()->user()->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));

        return [
            'accessToken' => $accessToken->plainTextToken,
            'refreshToken' => $refreshToken->plainTextToken,
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response(['message' => 'Successfully logged out'], 200);
    }

    public function refreshToken(Request $request)
    {
        $accessToken = $request->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
        return response(['message' => "Token generated", 'token' => $accessToken->plainTextToken]);
    }
}
