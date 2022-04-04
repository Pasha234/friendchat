<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SinglePageController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\MainController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('checkUser', [RegistrationController::class, 'checkUser']);
Route::get('getUser', [RegistrationController::class, 'getUser']);
Route::post('signup', [RegistrationController::class, 'signup']);
Route::post('login', [RegistrationController::class, 'login']);
Route::get('logout', [RegistrationController::class, 'logout']);
Route::post('changeNickname', [MainController::class, 'changeNickname']);

Route::get('/{any}', [SinglePageController::class, 'index'])->where('any', '.*');