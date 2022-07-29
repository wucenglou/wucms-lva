<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\IndexController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\UserController;

use App\Http\Controllers\Web\PostController;
use App\Http\Controllers\Web\RegisterController;

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

Route::get('/', [IndexController::class, 'index']);
// Route::get('/', [PostController::class, 'index']);

//登录页面   
Route::get('/login', [AuthController::class, 'index']);
//登录行为
Route::post('/login', [AuthController::class, 'login']);
//登出行为
Route::get('/logout', [AuthController::class, 'logout']);

//注册页面
Route::get('/register', [RegisterController::class, 'index']);
//注册行为
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/posts/search', [PostController::class, 'search']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/create', [PostController::class, 'create']);
Route::post('/posts', [PostController::class, 'store']);
Route::get('/posts/{post}', [PostController::class, 'show']);
Route::get('/posts/{post}/edit', [PostController::class, 'edit']);
Route::put('/posts/{post}', [PostController::class, 'update']);
Route::get('/posts/{post}/delete', [PostController::class, 'delete']);

Route::get('/c/{cat}/{name}', [PostController::class, 'cat']);

Route::post('/posts/image/upload', [PostController::class, 'imageUpload']);

Route::post('/posts/comment', [PostController::class, 'comment']);

// 个人设置页面
Route::get('/user/{user}/setting', [UserController::class, 'setting']);
//个人设置操作
Route::post('/user/{user}/setting', [UserController::class, 'settingStore']);

// Route::post('/posts/comment', "\App\Http\Controllers\PostController@comment");

Route::get('/posts/{post}/zan', '\App\Http\Controllers\PostController@zan');
Route::get('/posts/{post}/unzan', '\App\Http\Controllers\PostController@unzan');

// 个人主页
Route::get('/user/{user}', [UserController::class, 'show']);
Route::post('/user/{user}/fan', [UserController::class, 'fan']);
Route::post('/user/{user}/unfan', [UserController::class, 'unfan']);
