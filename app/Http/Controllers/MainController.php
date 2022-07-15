<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;

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

    public function changeAvatar(Request $request) {
      $validated = $request->validate([
        'avatar' => 'mimes:jpg,bmp,png',
      ]);
      if ($request->file('avatar')->isValid()) {
        $imgName = uniqid() . '.jpg';
        $path = $request->file('avatar')->storeAs('public', $imgName);
        $pathToImg = $_SERVER['DOCUMENT_ROOT'] . '/../storage/app/' . $path;
        $image = Image::make($pathToImg);
        if ($image->height() > $image->width()) {
          $image->widen(300);
        } else {
          $image->heighten(300);
        }
        $image->crop(300, 300);
        return response()->json(['success' => true, 'file' => $pathToImg]);
        $image->save();
        $user = Auth::user();
        if ($user->avatar) {
          Storage::disk('public')->delete($user->avatar);
        }
        $user->avatar = $imgName;
        $user->save();
        return response()->json(['success' => true, 'filename' => asset('storage/' . $imgName)]);
      }
    }
}
