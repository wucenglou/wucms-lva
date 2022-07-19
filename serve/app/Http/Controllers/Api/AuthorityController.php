<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Authority;
use App\Models\AuthorityMenu;
use App\Models\UserAuthority;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AuthorityController extends Controller
{

    public function getAuthorityList()
    {
        $user = Auth::guard('api')->user();
        $res = Authority::orderBy('created_at', 'asc')->get();

        // 给超级管理员控制所有角色的能力
        if($user['authority_id'] != 1){
            foreach ($user->authorities as $k => $v) {
                $arr[] = $v['authority_id'];
            }
            foreach ($res as $k => $v) {
                if (array_intersect([$res[$k]['authority_id']], $arr)) {
                    $res[$k]['disabled'] = false;
                } else {
                    $res[$k]['disabled'] = true;
                }
                if ($user['authority_id'] == $res[$k]['authority_id']) {
                    $res[$k]['disabled'] = true;
                }
            }
        }
        if (!(count($res) < 1)) {
            $res = $this->toHumpTree($res->toArray(), 'authority_id', 'parent_id');
        }
        return response()->json([
            'code' => 0,
            'data' => [
                'list' => $res,
            ],
            'message' => '获取成功'
        ]);
    }

    public function createAuthority(Request $request)
    {
        $res = $request->all();
        $r = Authority::create(array(
            'authority_name' => $res['authorityName'],
            'authority_sys' => $res['authoritySys'],
            'authority_describe' => $res['authorityDescribe'],
            'parent_id' => $res['parentId']
        ));
        AuthorityMenu::create(['authority_id' => $r['authority_id'], 'base_menu_id' => '1']);
        foreach ($r->toArray() as $k => $v) {
            $re[Str::camel($k)] = $v;
        }
        return response()->json([
            'code' => 0,
            'data' => [
                'list' => $re,
            ],
            'message' => '创建成功'
        ]);
    }

    public function deleteAuthority()
    {
        $authorityId = request('authorityId');
        $this->toTree(Authority::all(), $authorityId, 'authority_id', 'parent_id');
        $flag = Authority::where('authority_id', $authorityId)->orwherein('authority_id',$this->ar)->delete();
        AuthorityMenu::where('authority_id', $authorityId)->delete();
        if ($flag) {
            return response()->json([
                'code' => 0, 'data' => '', 'msg' => "删除成功"
            ]);
        } else {
            return response()->json([
                'code' => 0, 'data' => '', 'msg' => "要删除的数据不存在"
            ]);
        }
    }

    public function updateAuthority(Request $request)
    {
        $res = $request->all();
        $arr = $this->toLine([$res])['0'];
        $r = Authority::where('authority_id', $res['authorityId'])->update($arr);
        return response()->json([
            'code' => 0,
            'data' => [
                'authority' => $r,
            ],
            'message' => '更新成功'
        ]);
    }
}
