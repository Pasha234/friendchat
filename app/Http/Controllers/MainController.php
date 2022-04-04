<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MainController extends Controller
{
    public function changeNickname(Request $request) {
        $request->validate([
            'nickname' => 'required|max:255',
        ]);
        if (Auth::check()) {
          $id = Auth::id();
          $user = User::find($id);
          $user->name = $request->input('nickname');
          $user->save();
          return response()->json([
            'success' => true
          ]);
        }
        return response()->json([
          'errors' => [
            'nickname' => 'User is not authenticated'
          ]
        ]);
    }
}
