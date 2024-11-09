<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    // Sends the password reset link to the user's email
    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Retrieve the user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Generate the reset token
        $token = Password::createToken($user);

        return response()->json(['token' => $token], 200);
    }


    // Resets the password
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();

                // Optionally, invalidate other tokens if using Laravel Sanctum or Passport
                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been reset.'], 200);
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
