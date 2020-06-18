<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\user;

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

Route::POST('register', 'UserController@register');
Route::POST('login', 'UserController@login');
Route::GET('listUsers', 'UserController@listUsers');
Route::POST('recoverPassword', 'UserController@recoverPassword');
Route::POST('changePassword', 'UserController@changePassword');
Route::POST('deleteUser', 'UserController@deleteUser');



