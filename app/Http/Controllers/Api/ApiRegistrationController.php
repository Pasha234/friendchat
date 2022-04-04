<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ApiRegistrationController extends Controller
{
    public function signup(Request $request) {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users|max:255|email',
            'password' => 'required|max:255',
        ]);
        
        $user = new User;
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->save();

        $token = $user->createToken('myapptoken')->plainTextToken;
        return response()->json([
            'user' => [
                'id' => $user->id,
                'nickname' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ]);
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|max:255|email',
            'password' => 'required|max:255',
        ]);
        $user = User::where('email', $credentials['email'])
            ->first();
        
        if (Auth::attempt($credentials)) {
            $token = $user->createToken('myapptoken')->plainTextToken;
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'nickname' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ]);
        }
 
        return response()->json([
            'errors' => [
                'email' => 'The provided credentials do not match our records.'
            ]
        ]);
    }

    public function logout() {
        auth()->user()->tokens()->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function checkUser() {
        return response()->json(Auth::check());
    }

    public function getUser() {
        $user = Auth::user();
        return response()->json([
            'user' => $user,
            'token' => $user ? $user->createToken('myapptoken')->plainTextToken : null,
        ]);
    }
}
