<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAuthority;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Laravel\Passport\TokenRepository;
use Exception;
use Enforcer;

use App\Models\LoginLog;
use Jenssegers\Agent\Agent;
use Ip2Region;

class UserController extends Controller
{

    public function getUserInfo()
    {
        $user =  Auth::guard('api')->user();
        $res = $this->toHump([$user->toArray()])['0'];
        $res['authorities'] = $this->toHump($user->authorities->toArray());
        // return $res['authorities'];
        foreach ($res['authorities'] as $k => $v) {
            $arr[] = $v['authorityId'];
        }
        if (!array_intersect([$user['authority_id']], $arr)) {
            // 用户当前的角色和拥有的角色不匹配,则重置为已拥有角色中的其中一个。如果没有角色，则重置为初始0值
            $user->authority_id = $arr['0'] ?? 0;
            $user->save();
        }

        // return $arr;
        foreach ($res['authorities'] as $k => $v) {
            if ($v['authorityId'] == $user['authority_id']) {
                $res['authority'] = $v;
            }
        }
        if ($user) {
            return response()->json(['code' => 0, 'data' => ['userInfo' => $res], 'msg' => "登录成功"]);
        } else {
            return response()->json(['code' => 1, 'msg' => "获取失败"]);
        }
    }

    public function setUserAuthorities()
    {
        $res = request();
        // dd($res);
        $user = User::find($res['id']);
        $user->authorities()->sync($res['authorityIds']);
        if (!array_intersect([$user['authority_id']], $res['authorityIds'])) {
            // 用户当前的角色和拥有的角色不匹配,则重置为已拥有角色中的其中一个。如果没有角色，则重置为初始0值
            $user->authority_id = $res['authorityIds'][0] ?? 0;
            $user->save();
        }
        return response()->json([
            'code' => 0, 'data' => '', 'msg' => "修改成功"
        ]);
    }

    public function setUserAuthority()
    {
        $res = request();
        $user = Auth::guard('api')->user();
        $arr = $user->authorities;
        foreach ($arr as $v) {
            $ar[] = $v['authority_id'];
        }
        // 比较传入角色id和拥有的角色id是否匹配，如果不匹配，则不拥有此权限
        if (!array_intersect($ar, [$res['authorityId']])) {
            return response()->json([
                'code' => 1, 'data' => '', 'msg' => "权限不足，切换失败"
            ]);
        }
        $user->authority_id = $res['authorityId'];
        $user->save();
        return response()->json([
            'code' => 0, 'data' => '', 'msg' => "切换成功"
        ]);
    }

    public function login(Request $request)
    {
        // $rules = ['captcha' => 'required|captcha_api:' . request('captchaId')];
        // $validator = validator()->make(request()->all(), $rules);
        // if ($validator->fails()) {
        //     $this->loginlog($request, '验证码错误或过期', 0);
        //     return response()->json(['code' => 7, 'data' => '', 'msg' => "验证码错误或过期"]);
        // }
        // $request->validate([
        //     'username' => 'required|string',
        //     'password' => 'required|string',
        // ]);
        $credentials = request(['username', 'password']);
        if (!Auth::attempt($credentials)) {
            // $this->loginlog($request, '登录失败', 0);
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
        $user = $request->user();

        $tokenResult = $user->createToken("wucms-laravel");
        $token = $tokenResult->token;
        // 默认记住用户一周
        // if ($request->remember_me)
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        $this->loginlog($request, '登录成功');

        return response()->json(['code' => 0, 'data' => [
            'expiresAt' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'token' => $tokenResult->accessToken,
            'user' => $user
        ], 'msg' => "登录成功"], 200);
    }

    public function signup(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'realName' => 'nullable',
            'password' => 'required|string'
        ]);
        $user = new User([
            'username' => $request->username,
            'real_name' => $request->realName,
            'uuid' => Str::uuid(),
            'password' => bcrypt($request->password)
        ]);
        $user->save();
        // $user = $this->adapterUser($user);

        return response()->json([
            'code' => 0,
            'data' => [
                'user' => $this->toHump([$user->toArray()]),
            ],
            'message' => '用户创建成功!'
        ], 201);
    }


    /**
     * Undocumented function
     *
     * @return [string] message
     */
    public function getUserList()
    {

        $r = request();
        // dd($r);

        // 当前页 前端传过来
        $cur_page = request('page');
        $page_size = request('pageSize');
        // 一共多少数据
        $count = User::count();
        // 偏移量计算，从多少条开始算起
        $page = ($cur_page - 1) * $page_size;
        $status = $r['status'] ?? '';
        $authorityIds = request('authorityId') ?? [];
        // dd($authorityIds);
        $value = request("value") ?? '';
        // $users = User::where('username', 'like', "%$value%")->orwhereIn('status', [$r['status']])->orwhereIn('authority_id', $authorityIds)->orderBy('created_at', 'asc')->offset($page)->limit($page_size)->get();
        if ($authorityIds) {
            if ($status) {
                $users = User::where('status', $r['status'])->whereIn('authority_id', $authorityIds)->where('username', 'like', "%$value%")->orderBy('created_at', 'asc')->offset($page)->limit($page_size)->get();
            } else {
                $users = User::whereIn('authority_id', $authorityIds)->orderBy('created_at', 'asc')->where('username', 'like', "%$value%")->offset($page)->limit($page_size)->get();
            }
        } else {
            if ($status) {
                $users = User::where('status', $r['status'])->where('username', 'like', "%$value%")->orderBy('created_at', 'asc')->offset($page)->limit($page_size)->get();
            } else {
                $users = User::where('username', 'like', "%$value%")->orderBy('created_at', 'asc')->offset($page)->limit($page_size)->get();
            }
        }
        // if ($status) {
        // } else {
        //     $users = User::orwhereIn('authority_id', $authorityIds)->orderBy('created_at', 'asc')->offset($page)->limit($page_size)->get();
        // }
        $res = $this->toHump($users->toArray());
        foreach ($users as $k => $user) {
            $res[$k]['authorities'] = $this->toHump($user->authorities->toArray());
            $res[$k]['authority'] = $res[$k]['authorities']['0'] ?? '';
            // $res[$k]['avatarUrl'] = $res[$k]['avatarUrl'] ? env('APP_URL'). $res[$k]['avatar_url'] : '';
        }
        return response()->json([
            'code' => 0, 'data' => [
                'list' => $res,
                'page' => $cur_page,
                'pageSize' => $page_size,
                'total' => $count,
            ], 'msg' => "获取成功"
        ]);
    }

    /**
     * delete user
     *
     * @param Request $request
     * @return void
     */
    public function deleteUser(Request $request)
    {
        $id = $request->id;
        $flag = User::destroy($id);
        UserAuthority::where('user_id', $id)->delete();
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

    public function setUserInfo(Request $request)
    {
        $user =  Auth::guard('api')->user();
        $id = $request->id;
        $nickName = $request->nickName;
        $sideMode = $request->sideMode;

        // 如果是设置主题则。。。否则就是重设昵称
        if (isset($sideMode) && $user) {
            $flag = User::where('id', $user->id)->update(array("side_mode" => $sideMode));
        } else {
            $flag = User::where('id', $id)->update(array("nick_name" => $nickName));
        }
        if ($flag) {
            return response()->json([
                'code' => 0, 'data' => '', 'msg' => "更新成功"
            ]);
        } else {
            return response()->json([
                'code' => 0, 'data' => '', 'msg' => "更新失败"
            ]);
        }
    }

    public function resetPassword(Request $request)
    {
        $id = $request->all();
        return response()->json([
            'code' => 1, 'data' => '', 'msg' => "功能未完善"
        ]);
    }


    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {


        $tokenRepository = app(TokenRepository::class);
        $tokenRepository->revokeAccessToken(Auth::guard('api')->user()->token()->id);
        // $request->user()->token()->revoke();
        return response()->json([
            'code' => 0,
            'message' => 'Successfully logged out'
        ]);
    }

    public function option(Request $request)
    {
        $r = request();
        $user =  Auth::guard('api')->user();
        // dd($r);
        switch ($r['option']) {
            case "0":
                $res = User::whereIn('id', $r['userIds'])->update(['status' => $r['option']]);
                return $this->response($res);
                break;
            case "1":
                $res = User::whereIn('id', $r['userIds'])->update(['status' => $r['option']]);
                return $this->response($res);
                break;
            case "Delete":
                $res = User::whereIn('id', $r['userIds'])->delete();
                return $this->response($res, "删除" . $res . "条数据");
            default:
                return $this->response('250');
        }
    }

    public function log()
    {
        // 当前页 前端传过来
        $page = request('page');
        $pageSize = request('pageSize');
        // 一共多少数据
        $total = LoginLog::all()->count();
        // 偏移量计算，从多少条开始算起
        $page = ($page - 1) * $pageSize;

        $res = LoginLog::orderBy('created_at', 'desc')->offset($page)->limit($pageSize)->get();
        foreach ($res as $k => $v) {
            $res[$k]['browserInfo'] = $res[$k]['browser'] . ' ' . $res[$k]['browser_ver'];
            $res[$k]['platformInfo'] = $res[$k]['platform'] . ' ' . $res[$k]['platform_ver'];
            $res[$k]['deviceInfo'] = $res[$k]['device_type'] . ' ' . $res[$k]['device'];
            $res[$k]['createdAt'] = $res[$k]['created_at']->format('Y-m-d h:i:s');
            $res[$k]->makeHidden('browser', 'browserVer', 'device', 'deviceType', 'platform', 'platformVer');
        }
        return $this->response([
            'list' => $this->toHump(
                $res->toArray(),
            ),
            'page' => request('page'),
            'pageSize' => request('pageSize'),
            'total' => $total,
        ]);
    }


    // public function adapterUser($user)
    // {
    //     try {
    //         $length = count($user);
    //     } catch (Exception $e) {
    //         $length = 0;
    //     }
    //     if ($length) {
    //         for ($i = 0; $i < $length; $i++) {
    //             $items[$i]['ID'] = $user[$i]['id'];
    //             $items[$i]['uuid'] = $user[$i]['uuid'];
    //             $items[$i]['userName'] = $user[$i]['username'];
    //             $items[$i]['nickName'] = $user[$i]['nick_name'];
    //             $items[$i]['headerImg'] = $user[$i]['avatar_url'];
    //             $items[$i]['baseColor'] = $user[$i]['base_color'];
    //             $items[$i]['sideMode'] = $user[$i]['side_mode'];
    //             $items[$i]['authorityId'] = $user[$i]['authority_id'];
    //             $items[$i]['activeColor'] = $user[$i]['active_color'];
    //             $items[$i]['authorities'] = "";
    //             $items[$i]['authority'] = "";
    //         }
    //         return $items;
    //     } else {
    //         if (isset($user['id'])) {
    //             $item['ID'] = $user['id'];
    //             $item['uuid'] = $user['uuid'];
    //             $item['userName'] = $user['username'];
    //             $item['nickName'] = $user['nick_name'];
    //             $item['headerImg'] = $user['avatar_url'];
    //             $item['baseColor'] = $user['base_color'];
    //             $item['sideMode'] = $user['side_mode'];
    //             $item['authorityId'] = $user['authority_id'];
    //             $item['activeColor'] = $user['active_color'];
    //             $item['authorities'] = "";
    //             $item['authority'] = "";
    //             return $item;
    //         } else {
    //             return [];
    //         }
    //     }
    // }
}
