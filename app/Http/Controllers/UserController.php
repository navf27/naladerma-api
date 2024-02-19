<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'max:255',
            'email' => 'unique:users|email|max:255',
            'phone' => 'max:20',
            'address' => 'max:255',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->all());

        $response['status'] = true;
        $response['message'] = 'Update user success.';
        $response['data'] = $user;

        return response()->json($response);
    }
}
