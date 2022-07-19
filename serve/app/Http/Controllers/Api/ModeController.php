<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mode;
use Illuminate\Support\Facades\Validator;


class ModeController extends Controller
{
    //
    public function addMode(Request $request)
    {
        $r = $request->all();
        $validator = Validator::make($r , [
            'name' => 'required|max:10',
            'sort' => 'required',
        ], [
            'name.max' => '最大为10个字符'
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 1, 'data' => '', 'msg' => $validator->errors()->first()]);
        }
        $flag = Mode::create($this->toLine([$r])[0]);
        return $this->response($flag);
    }

    public function modeById(Request $request)
    {
        $r = $request->all();
        if ($request->method() == 'PUT') {
            $res = Mode::where('id', $r['id'])->update($this->toLine([$r])[0]);
            return $this->response($res,'更新');
        } elseif ($request->method() == 'DELETE') {
            // 通过一个id删除模型及其子模型
            $this->toTree(Mode::all(), $r[0], 'id', 'parent_id');
            $res = Mode::where('id', $r)->orwherein('id', $this->ar)->delete();
            return $this->response($res,'删除');
        }
    }

    public function modeList(Request $request)
    {
        $res = Mode::all();
        return response()->json(['code' => 0, 'data' => [
            'list' => $this->toHumpTree($res->toArray(), 'id', 'parent_id'),
        ], 'msg' => "获取成功"]);
    }
}
