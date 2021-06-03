<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LowonganController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPostController;

use App\Models\User;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'regStage1']);
Route::get('/getUser/{id}', [UserController::class, 'getUserWithId']);

Route::get('/post', [UserPostController::class, 'getAll']);
Route::get('/get_post_owner/{id}', [UserPostController::class, 'getSpecificOwner']);
Route::get('/get_post_random', [UserPostController::class, 'getRandom']);

Route::get('/lowongan', [LowonganController::class, 'getAll']);
Route::get('/get_lowongan_owner/{id}', [LowonganController::class, 'getSpecificOwner']);
Route::get('/get_lowongan_random', [LowonganController::class, 'getRandom']);

Route::group(['middleware' => 'auth:sanctum'], function(){
  Route::post('/getUser', [UserController::class, 'getUser']);

  Route::post('/edit_profile', [UserController::class, 'edit']);
  Route::post('/delete_account', [UserController::class, 'delAcc']);

  Route::post('/logout', [UserController::class, 'logout']);
  
  Route::get('/follow', [UserController::class, 'checkFollow']);
  Route::post('/follow', [UserController::class, 'follow']);

  Route::post('/create_lowongan', [LowonganController::class, 'create']);
  Route::post('/update_lowongan', [LowonganController::class, 'update']);
  Route::post('/delete_lowongan', [LowonganController::class, 'drop']);

  Route::post('/create_post', [UserPostController::class, 'create']);
  Route::post('/update_post', [UserPostController::class, 'update']);
  Route::post('/delete_post', [UserPostController::class, 'drop']);

  Route::post('/lamar', [LowonganController::class, 'lamar']);
  Route::post('/like', [UserPostController::class, 'like']);
  Route::post('/unlike', [UserPostController::class, 'unlike']);
});