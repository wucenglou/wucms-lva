<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    //
    // 注册页面
    public function index()
    {
        // $request->validate([
        //     'username' => 'required|string',
        //     'realName' => 'nullable',
        //     'password' => 'required|string'
        // ]);
        // $user = new User([
        //     'username' => $request->username,
        //     'real_name' => $request->realName,
        //     'uuid' => Str::uuid(),
        //     'password' => bcrypt($request->password)
        // ]);
        // $user->save();
        // $user = $this->adapterUser($user);

        return view('register/index');
    }

    public function register()
    {
        $this->validate(request(), [
            'username' => 'required|min:2|unique:users,username',
            'email' => 'required|unique:users,email|email',
            'password' => 'required|min:5|confirmed',
        ]);

        $password = bcrypt(request('password'));
        $username = request('username');
        $email = request('email');
        $uuid = Str::uuid();
        $user = User::create(compact('username', 'email','uuid', 'password'));
        return redirect('/login');

    }
}
