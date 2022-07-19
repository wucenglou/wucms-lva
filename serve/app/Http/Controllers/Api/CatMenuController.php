<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\PostDynamic;

class CatMenuController extends Controller
{
    //
    public function catList()
    {
        $r = CatMenu::all();

        return $this->response(['list' => $this->toHumpTree($r->toArray(), 'id', 'parent_id')]);
    }
    public function catmenu(Request $request)
    {
        $r = $request->all();
        $validator = Validator::make($r, [
            'name' => 'required|max:100',
            'sort' => 'required',
        ], [
            'name.max' => '最大为100个字符'
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 1, 'data' => '', 'msg' => $validator->errors()->first()]);
        }
        if ($request->method() == 'PUT') {
            
            $res = CatMenu::where('id', $r['id'])->update($this->toLine([$r])[0]);
        } elseif ($request->method() == 'POST') {

            $res = CatMenu::create($this->toLine([$r])[0]);
        }
        return $this->response($res);
    }

    public function deleteCatMenu(Request $request)
    {
        $r = $request->all();
        // 通过一个id删除模型及其子模型
        $this->toTree(CatMenu::all(), $r[0], 'id', 'parent_id');
        if($this->ar){
            return $this->response([], '存在子分类，删除');
        }else {
            $r = CatMenu::find($r[0]);
            PostDynamic::table($r->mode['table'])->where('cat_id', $r['id'])->delete();
            $r->delete();
            return $this->response($r, '删除');
        }
        return request();
    }

}
