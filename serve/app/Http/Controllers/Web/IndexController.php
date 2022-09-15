<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CatMenu;
use Illuminate\Support\Facades\Auth;
use App\Models\PostArticle;

class IndexController extends Controller
{
    public function index()
    {
        $navss = CatMenu::all();
        $navs = $this->toHumpTree($navss->toArray(),'id','parent_id');
        // return $navs;
        // return view('welcome',compact('navs'));

        $user = Auth::user();
        $posts = PostArticle::orderBy('created_at', 'desc')->paginate(10);
        $topics = [];
        // dd($posts->user);
        // $posts = [];
        // dd($posts);
        $cat = [];
        return view("post/index", compact('posts', 'topics','navs', 'cat'));
    }

    
}
