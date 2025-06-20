<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;
use App\Http\Resources\UserResource;
use App\Http\Resources\ChatResource;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(
            UserResource::collection(User::all())
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json(
                new UserResource($user)
            );
        } else {
            return response()->json([
                'error' => 'User with the given id is not found'
            ], 404);
        }
    }

    /**
     * Search for users with specified string
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if ($request->filled('s')) {
            if ($request->filled('p')) {
                $page = $request->input('p');
            } else {
                $page = 1;
            }
            $users = User::where('name', 'like', '%' . $request->input('s') . '%');

            return [
                'count' => $users->count(),
                'data' => UserResource::collection(
                    $users->skip(($page-1) * 10)
                    ->take(10)
                    ->get()
                ),
            ];
        } else {
            return response()->json([
                'error' => 'Missing search string'
            ]);
        }
    }
}
