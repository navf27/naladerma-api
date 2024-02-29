<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
        $response['message'] = 'Login success.';
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
            'password' => 'required|min:6|max:255',
        ]);

        $user = User::create($validated);

        $response['status'] = true;
        $response['message'] = 'Register success.';
        $response['data'] = $user;

        return response()->json($response);
    }

    public function emailForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users|email',
        ]);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        Mail::send('emails.forgotPassword', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject("Naladerma Reset Password");
        });

        return response()->json([
            'status' => true,
            'message' => 'Request token success.',
            'data' => $token,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:255|confirmed',
            'password_confirmation' => 'required|min:6|max:255',
        ]);

        $tokenData = DB::table('password_reset_tokens')->where([
            'email' => $request->email,
            'token' => $request->token,
        ])->first();

        if (!$tokenData) {
            return response()->json([
                'status' => false,
                'message' => 'Token not found.',
            ]);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where([
            'email' => $request->email,
            'token' => $request->token,
        ])->delete();

        return response()->json([
            'status' => true,
            'message' => 'Reset password success.',
            'data' => $user,
        ]);
    }

    public function me()
    {
        $response['status'] = true;
        $response['data'] = auth()->user();
        return response()->json($response);
    }
}
