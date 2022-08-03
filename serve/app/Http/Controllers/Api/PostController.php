<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatMenu;
use App\Models\Img;
use App\Models\PostArticle;
use App\Models\PostDynamic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    //
    public function store(Request $request)
    {
        $re = $request->all();
        $r = json_decode($re['form'], true);
        unset($r['picList']);
        $files = $re['files'] ?? '';
        $cur_user = Auth::guard('api')->user();
        $r['user_id'] = empty($r['user_id']) ? $cur_user['id'] : $r['user_id'];
        $r['mode_id'] = empty($r['mode_id']) ?  CatMenu::find($r['catId'])['mode_id'] : $r['mode_id'];
        $table = DB::table('modes')->find($r['mode_id'])->table;
        if (isset($r['id'])) {
            $res = PostDynamic::table($table)->where('id', $r['id'])->update($this->toLine([$r])[0]);
            $this->imgsUpload($files,$r['id'],$r['mode_id']);
        } else {
            // 通过模型id查到该模型专有的数据表，
            $r['created_at'] = now();
            $r['updated_at'] = now();
            $res = PostDynamic::table($table)->insertGetId($this->toLine([$r])[0]);
            $this->imgsUpload($files,$res,$r['mode_id']);
            // $res = PostDynamic::table($table)->create($this->toLine([$r])[0]);
            // $res = PostArticle::create($this->toLine([$r])[0]);
        }
        return $this->response($res);
    }

    public function imgsUpload($imgs, $post_id, $mode_id)
    {
        if($imgs){
            foreach ($imgs as $img) {
                $name = $img->getClientOriginalName();
                // 搜索‘.’最后出现的位置
                $end = strripos($name, ".");
                // 截取‘.’之前的字符串，达到去掉扩展名的目的
                $name = substr($name, 0, $end);
                $path = Storage::disk('public')->put('/imgs', $img);
                $url =  '/storage/' . $path;
                Img::create(compact('url', 'name', 'post_id', 'mode_id'));
            }
        }
    }

    public function getPost(Request $request)
    {
        $r = $request->all();
        $postid = $r['post_id'] ?? null;
        $modeid = $r['mode_id'] ?? null;
        if ($postid && $modeid) {
            $table = DB::table('modes')->find($modeid)->table;
            $res = PostDynamic::table($table)->where('id', $postid)->get();
            $res[0]['picList'] = Img::where('post_id',$res[0]['id'])->where('mode_id',$modeid)->get();
            foreach($res[0]['picList'] as $r){
                $r['url'] =  env('APP_IMGS_URL').$r['url'];
            }
            // dd($res[0]["id"]);
            // dd($res);
            return $this->response($this->toHump($res->toArray()));
        }
    }

    public function getPosts()
    {
        $r = request();
        // dd($r);
        // 当前页 前端传过来
        $cur_page = request('page');
        $page_size = request('pageSize');
        $catIds = request('catIds');
        $status = request('status');
        $routeName = request("routeName") ?? false;
        $value = request("value") ?? '';
        // 偏移量计算，从多少条开始算起
        $page = ($cur_page - 1) * $page_size;
        // 一共多少数据
        $menuInfo = explode("-", $routeName);
        if (empty($catIds) && count($menuInfo) > 2) {
            $catIds = [$menuInfo[2]];
        }

        // 通过模型id查到该模型专有的数据表，
        $table = DB::table('modes')->find($r['modeId'])->table;
        if (empty($catIds)) {
            if (is_null($status)) {
                $res = PostDynamic::table($table)->where('title', 'like', "%$value%")->orderBy('created_at', 'desc')->offset($page)->limit($page_size)->get();
                $count = PostDynamic::table($table)->where('title', 'like', "%$value%")->count();
            } else {
                $res = PostDynamic::table($table)->where('status', $status)->where('title', 'like', "%$value%")->orderBy('created_at', 'desc')->offset($page)->limit($page_size)->get();
                $count = PostDynamic::table($table)->where('status', $status)->where('title', 'like', "%$value%")->count();
            }
        } else {
            if (is_null($status)) {
                $res = PostDynamic::table($table)->whereIn('cat_id', $catIds)->where('title', 'like', "%$value%")->orderBy('created_at', 'desc')->offset($page)->limit($page_size)->get();
                $count = PostDynamic::table($table)->whereIn('cat_id', $catIds)->where('title', 'like', "%$value%")->count();
            } else {
                $res = PostDynamic::table($table)->whereIn('cat_id', $catIds)->where('status', $status)->where('title', 'like', "%$value%")->orderBy('created_at', 'desc')->offset($page)->limit($page_size)->get();
                $count = PostDynamic::table($table)->whereIn('cat_id', $catIds)->where('status', $status)->where('title', 'like', "%$value%")->count();
            }
        }

        foreach ($res as $re) {
            $re->makeHidden(['content']);
            $re['author_name'] = $re->user['username'] ?? '';
            $re['mode_name'] = $re->mode['name'] ?? '';
            $re['cat_name'] = $re->cat['meta_title'] ?? '分类ID'.$re['cat_id'].'已删除';
            switch ($re['status']) {
                case 1:
                    $re['status_name'] = "已发布";
                    break;
                case 0:
                    $re['status_name'] = "待审核";
                    break;
                case -1:
                    $re['status_name'] = "审核未通过";
                    break;
                case -2:
                    $re['status_name'] = "草稿";
                    break;
                case 2:
                    $re['status_name'] = "已过期";
                    break;
                default:
                    $re['status_name'] = "未知状态";
            }
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

    public function deletePost(Request $request)
    {
        $r = request();
        // 通过模型id查到该模型专有的数据表，
        $table = DB::table('modes')->find($r['modeId'])->table;
        $res = PostDynamic::table($table)->whereIn('id', $r['ids'])->delete();
        return $this->response($res);
    }

    public function optionsPost()
    {
        $r = request();
        // 通过模型id查到该模型专有的数据表，
        $table = DB::table('modes')->find($r['modeId'])->table;
        if ($r['type'] == "Delete") {
            $res = PostDynamic::table($table)->whereIn('id', $r['ids'])->delete();
        } else {
            $res = PostDynamic::table($table)->whereIn('id', $r['ids'])->update(['status' => $r['type']]);
        }
        return $this->response($res);
    }
}
