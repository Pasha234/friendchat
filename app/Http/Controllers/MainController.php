<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use Cloudinary;

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
        // $path = $request->file('avatar')->storeAs('public', $imgName);
        $path = $request->file('avatar')->getPathName();
        // $pathToImg = $_SERVER['DOCUMENT_ROOT'] . '/../storage/app/' . $path;
        // $image = Image::make($pathToImg);
        $image = Image::make($path);
        if ($image->height() > $image->width()) {
          $image->widen(300);
        } else {
          $image->heighten(300);
        }
        $image->crop(300, 300);
        $image->save();
        // $yourFile = new UploadedBase64EncodedFile(new Base64EncodedFile($image->toString())); 
        // $imgRaw = imagecreatefromstring( $image->__toString() );
        // if ($imgRaw !== false) {
        //     imagejpeg($imgRaw, storage_path().'/tmp/tmp.jpg',100);
        //     imagedestroy($imgRaw);
        //     $file =  new UploadedFile( storage_path().'/tmp/tmp.jpg', 'tmp.jpg', 'image/jpeg',null,null,true);
        //     // DO STUFF WITH THE UploadedFile
        // }
        // dd($image->__toString());
        $uploadedFileUrl = Cloudinary::upload($path)->getSecurePath();
        $user = Auth::user();
        if ($user->avatar) {
          // Storage::disk('public')->delete($user->avatar);
          $url_path = explode('/', 
            parse_url($user->avatar)['path']
          );
          cloudinary()->uploadApi()->destroy(
            explode(".", 
              end($url_path)
            )[0]
          );
        }
        $user->avatar = $uploadedFileUrl;
        $user->save();
        return response()->json(['success' => true, 'filename' => $uploadedFileUrl]);
      }
    }
}
