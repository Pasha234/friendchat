<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ApiRegistrationController;
use App\Http\Controllers\MainController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/





// Route::get('user/{id}/chats', [UserController::class, 'chats']);

// Public routes
Route::post('/register', [ApiRegistrationController::class, 'signup']);
Route::post('/login', [ApiRegistrationController::class, 'login']);

Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Protected routes
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('chat/to/{user_id}/from/{user_id_2}/messages', [ChatController::class, 'showMessages']);
    Route::post('messages/send', [ChatController::class, 'sendMessage']);
    Route::get('user/chats', [ChatController::class, 'userChats']);
    Route::post('/logout', [ApiRegistrationController::class, 'logout']);
    Route::get('user/check', [ApiRegistrationController::class, 'checkUser']);
    Route::get('user/get', [ApiRegistrationController::class, 'getUser']);
    Route::post('user/changeAvatar', [MainController::class, 'changeAvatar']);
    Route::get('users/search', [UserController::class, 'search'])
        ->name('users.search');
    Route::apiResources([
        'users' => UserController::class,
        'chats' => ChatController::class,
    ]);
});
