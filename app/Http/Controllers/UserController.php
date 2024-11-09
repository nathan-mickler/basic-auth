<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function view(Request $request)
    {
        $user = $request->user();
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2'
        ]);

        $user = $request->user();
        $name = $request->post('name');

        $user->update([
            'name' => $name
        ]);

        return response()->json($user);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        $user->delete();

        return response()->json(['message' => 'User deleted.']);
    }
}
