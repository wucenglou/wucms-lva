<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CatMenu;
use App\Models\PostArticle;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    // 列表
    public function index()
    {
        $user = \Auth::user();
        $posts = PostArticle::orderBy('created_at', 'desc')->paginate(6);
        $topics = [];
        return view("post/index", compact('posts', 'topics'));
    }

    //详情页面
    public function show(PostArticle $post)
    {
        // 预加载
        $post->load('comments');
        // $cnum = $post->cnum + 1;
        // PostArticle::where('id', $post->id)->update(compact('cnum'));
        return view('post/show', compact('post'));
    }

    // 发表评论
    public function comment(Request $request)
    {
        $this->validate(request(), [
            'cat_id' => 'required|exists:cat_menus,id',
            'post_id' => 'required|exists:post_articles,id',
            'content' => 'required|min:2',
        ]);
        $r = request();
        $mode_id = CatMenu::find($r['cat_id'])['mode_id'];
        $userip = request()->ip();
        $user_id = \Auth::id();
        $user_agent = $request->userAgent();
        $params = array_merge(
            request(['post_id', 'content', 'cat_id']),
            compact('user_id', 'mode_id', 'userip', 'user_agent')
        );
        Comment::create($params);
        return back();
    }







}
