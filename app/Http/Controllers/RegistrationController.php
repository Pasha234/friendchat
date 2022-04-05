<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Resources\OwnerResource;

class RegistrationController extends Controller
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
        Auth::loginUsingId($user->id);
        return response()->json([
            'user' => [
                'id' => $user->id,
                'nickname' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|max:255|email',
            'password' => 'required|max:255',
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'nickname' => $user->name,
                    'email' => $user->email,
                ]
            ]);
        }
 
        return response()->json([
            'errors' => [
                'email' => 'The provided credentials do not match our records.'
            ]
        ]);
    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'success' => true
        ]);
    }

    public function checkUser() {
        return response()->json(Auth::check());
    }

    public function getUser() {
        if (Auth::check()) {
            return response()->json([
                'user' => new OwnerResource(Auth::user())
            ]);    
        }
        return response()->json([
            'user' => null
        ]);
    }
}