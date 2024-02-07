<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'message' => ["The provided credentials are incorrect!"],
            ]);
        }

        $response['status'] = true;
        $response['token'] = $user->createToken('user_login')->plainTextToken;

        return response()->json([$response]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $response['status'] = true;
        $response['message'] = 'logout success';

        return response()->json($response);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users|email|max:255',
            'phone' => 'required|max:20',
            'address' => 'required|max:255',
            'password' => 'required|max:255',
        ]);

        $user = User::create($validated);

        $response['status'] = true;
        $response['message'] = 'Register success.';
        $response['data'] = $user;

        return response()->json($response);
    }

    public function me()
    {
        $response['status'] = true;
        $response['data'] = auth()->user();
        return response()->json($response);
    }
}
