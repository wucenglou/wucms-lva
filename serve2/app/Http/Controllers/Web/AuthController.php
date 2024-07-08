<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class AuthController extends Controller
{

    public function index()
    {
        return view("login/index");
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['username', 'password']);
        if (!Auth::attempt($credentials)){
            $this->loginlog($request, '验证失败', 0);
            return \Redirect::back()->withErrors("验证失败");
        }
        $this->loginlog($request, '登录成功');

        return redirect('posts');
        // $user = $request->user();
        // $tokenResult = $user->createToken("wucms-laravel");
        // $token = $tokenResult->token;
        // if ($request->remember_me)
        //     $token->expires_at = Carbon::now()->addWeeks(1);
        // $token->save();
    }

    public function logout()
    {
        \Auth::logout();
        return redirect('/login');
    }

}
