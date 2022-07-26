<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\AuthorityController;
use App\Http\Controllers\Api\CasbinController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ModeController;
use App\Http\Controllers\Api\CatMenuController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;

use App\Http\Controllers\Common\FileController;
use App\Models\PostArticle;
use Illuminate\Support\Facades\Auth;
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

Route::get('test', function () {
    $posts = PostArticle::orderBy('created_at', 'desc')->paginate(100);
    $topics = [];
    foreach ($posts as $post) {
        // foreach($post->user as $user){
        // print_r($user);
        // }
        print_r($post->user->real_name);
        print_r("-----++++++++++-----");
    }
    // return '6666';
});

Route::prefix('admin')->group(function () {
});

// 模型
Route::post('mode', [ModeController::class, 'addmode']);
Route::get('mode', [ModeController::class, 'getmode']);
Route::match(['get', 'put', 'delete'], 'modeById', [ModeController::class, 'modeById']);
Route::get('modeList', [ModeController::class, 'modeList']);

// 栏目
Route::get('catList', [CatMenuController::class, 'catList']);
Route::match(['post', 'put'], 'catmenu', [CatMenuController::class, 'catmenu']);
Route::delete('catmenu', [CatMenuController::class, 'deletecatmenu']);

// 提交
Route::post('post', [PostController::class, 'store']);
Route::post('getpost', [PostController::class, 'getPost']);
Route::delete('post', [PostController::class, 'deletePost']);

Route::post('optionspost', [PostController::class, 'optionsPost']);

// 查询
Route::post('posts', [PostController::class, 'getPosts']);

Route::prefix('base')->group(function () {
    Route::match(['get', 'post'], 'captcha', [BaseController::class, 'captcha']);
    Route::post('login', [UserController::class, 'login']);
});

// 评论管理
Route::post('comment', [CommentController::class, 'getComments']);



// 文件仓库
Route::post('uploadimgs', [FileController::class, 'uploadimgs']);

// 权限控制
Route::prefix('casbin')->group(function () {
    Route::post('updateCasbin', [CasbinController::class, 'updateCasbin']);
    Route::post('getPolicyPath', [CasbinController::class, 'getPolicyPath'])->middleware('casbin');
});
Route::prefix('api')->group(function () {
    Route::post('initAddApi', [ApiController::class, 'initAddApi']);
    Route::post('getAllApis', [ApiController::class, 'getAllApis']);
    Route::post('getApiList', [ApiController::class, 'getApiList']);
});
Route::prefix('user')->group(function () {
    // Route::post('login', [UserController::class, 'login']);
    Route::post('t', [UserController::class, 't']);
    Route::post('signup', [UserController::class, 'signup']);
    Route::post('register', [UserController::class, 'signup']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('getUserInfo', [UserController::class, 'getUserInfo']);
    Route::post('getUserList', [UserController::class, 'getUserList']);
    Route::delete('deleteUser', [UserController::class, 'deleteUser']);
    Route::put('setUserInfo', [UserController::class, 'setUserInfo']);
    Route::post('resetPassword', [UserController::class, 'resetPassword']);
    Route::post('setUserAuthorities', [UserController::class, 'setUserAuthorities'])->middleware('casbin');
    Route::post('setUserAuthority', [UserController::class, 'setUserAuthority']);
    Route::post('option', [UserController::class, 'option']);
    Route::post('loginlog', [UserController::class, 'log']);
});
Route::prefix('authority')->group(function () {
    Route::post('getAuthorityList', [AuthorityController::class, 'getAuthorityList']);
    Route::post('createAuthority', [AuthorityController::class, 'createAuthority']);
    Route::post('deleteAuthority', [AuthorityController::class, 'deleteAuthority']);
    Route::put('updateAuthority', [AuthorityController::class, 'updateAuthority']);
});
Route::any('t', function () {
    dd(Auth::guest());
});
Route::prefix('menu')->group(function () {
    Route::post('getMenu', [MenuController::class, 'getMenu']);
    Route::post('getMenuList', [MenuController::class, 'getMenuList']);
    Route::post('addBaseMenu', [MenuController::class, 'addBaseMenu']);
    Route::post('getBaseMenuById', [MenuController::class, 'getBaseMenuById']);
    Route::post('deleteBaseMenu', [MenuController::class, 'deleteBaseMenu']);
    Route::post('updateBaseMenu', [MenuController::class, 'updateBaseMenu']);
    Route::post('getBaseMenuTree', [MenuController::class, 'getMenu']);
    Route::post('getMenuAuthority', [MenuController::class, 'getMenuAuthority']);
    Route::post('addMenuAuthority', [MenuController::class, 'addMenuAuthority']);
});

Route::post('casbin/getPolicyPathByAuthorityId', []);




// Route::group(['prefix' => 'auth'], function () {
//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('signup', [AuthController::class, 'signup']);

//     Route::group(['middleware' => 'auth:api'], function () {
//         Route::get('logout', [AuthController::class, 'logout']);
//         Route::get('user', [AuthController::class, 'user']);
//     });
// });
