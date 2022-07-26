<?php

namespace App\Http\Controllers\APi;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    //
    public function getComments(Comment $comment)
    {
        // $r = request();
        // dd($r);
        // 当前页 前端传过来
        $cur_page = request('page');
        $page_size = request('pageSize');
        $catIds = request('catIds');
        $status = request('status');
        // 偏移量计算，从多少条开始算起
        $page = ($cur_page - 1) * $page_size;
        // 一共多少数据
        $count = Comment::count();
        $res = Comment::orderBy('created_at', 'desc')->offset($page)->limit($page_size)->get();
        foreach($res as $re){
            $re['mode_name'] = $re->mode->name;
            $re['cat_name'] = $re->cat->meta_title;
            $re['title'] = $re->post->title ?? '文章已删除';
            $re['author_name'] = $re->user->username ?? '此用户已注销';
        }

        return $this->response([
            'list' => $this->toHump($res->toArray()),
            'page' => $cur_page,
            'pageSize' => $page_size,
            'total' => $count,
            'searchInfo' => [
                'status' => request('status'),
                // 'catId' => request('catId'),
                // 'id' => request('id'),
                // 'keyword' => request('keyword'),
                // 'modeId' => request('modeId'),
            ],
        ]);
    }
}
