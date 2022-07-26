<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Fan;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CatMenu;

class UserController extends Controller
{
    //
    public function show(User $user)
    {
        $navss = CatMenu::all();
        $navs = $this->toHumpTree($navss->toArray(), 'id', 'parent_id');

        $posts = $user->posts()->orderBy('created_at', 'desc')->take(5)->get();
        $user = User::withCount(['stars', 'fans', 'posts'])->find($user->id);
        $fans = $user->fans()->get();
        $stars = $user->stars()->get();
        //dd($stars);
        return view("user/show", compact('user', 'posts', 'fans', 'stars','navs'));
    }

    public function fan(User $user)
    {
        $me = \Auth::user();
        Fan::firstOrCreate(['fan_id' => $me->id, 'star_id' => $user->id]);
        return [
            'error' => 0,
            'msg' => ''
        ];
    }

    public function unfan(User $user)
    {
        $me = \Auth::user();
        Fan::where('fan_id', $me->id)->where('star_id', $user->id)->delete();
        return [
            'error' => 0,
            'msg' => ''
        ];
    }

    public function setting($user)
    {
        $navss = CatMenu::all();
        $navs = $this->toHumpTree($navss->toArray(), 'id', 'parent_id');

        $me = \Auth::user();
        return view('user/setting', compact('me','navs'));
    }

    public function settingStore(Request $request, User $user)
    {
        $this->validate(request(), [
            'username' => 'min:3',
        ]);

        $name = request('username');
        if ($name !=  $user->username) {
            if (User::where('username', $name)->count() > 0) {
                return back()->withErrors(array('message' => '用户名已经注册'));
            }
            $user->name = request('name');
        }
        if ($request->file('avatar_url')) {
            $path = $request->file('avatar_url')->store('public/web/'.md5(\Auth::id() . time()));
            // dd($path);
            $user->avatar_url = "/storage/" . ltrim($path,'public/');
        }
        $user->save();
        return back();
    }
}
