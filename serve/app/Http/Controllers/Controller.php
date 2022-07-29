<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Enforcer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\MenuParameter;

use App\Models\LoginLog;
use App\Models\User;
use Jenssegers\Agent\Agent;
use Ip2Region;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function loginlog($request, $msg = '', $status = '1')
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());
        $useragent = $request->userAgent();
        $ip2region = new Ip2Region();
        $info = $ip2region->btreeSearch($request->ip());
        $ip = $request->ip();
        $address = $info['city_id'] . '|' . $info['region'];
        $platform = $agent->platform();
        $platform_ver = $agent->version($platform);
        $browser = $agent->browser();
        $browser_ver = $agent->version($browser);
        $device = $agent->device();
        $device_type = $agent->deviceType();

        $Power_user = User::where('username', $request->username)->first();
        $uid = $Power_user->id ?? '';
        $username = $request->username ?? '';
        if (empty($Power_user)) {
            $msg = "未找到此用户";
        }

        LoginLog::create(compact('uid', 'username', 'address', 'ip', 'useragent', 'platform', 'platform_ver', 'browser', 'browser_ver', 'device', 'device_type', 'msg', 'status'));
    }

    /**
     * Casbin 权限验证
     *
     * @param string $userGuard 用户守卫
     * @param string $casbinGuard casbin守卫
     * @return void
     */
    public function UserPower($userGuard = 'api', $casbinGuard = '')
    {
        $Power_req = request()->server();
        $Power_user = Auth::guard($userGuard)->user();
        if (!Enforcer::enforce($Power_user['authority_id'], $Power_req['REQUEST_URI'], $Power_req['REQUEST_METHOD'])) {
            abort(response()->json([
                'code' => 403, 'data' => '', 'msg' => "权限不足"
            ]));
        }
    }

    /**
     * 响应封装
     *
     * @param array $data 返回前端的数据数组
     * @param string $msg 操作信息
     * @param integer $codeA 自定义响应码 0：代表操作成功，1：代表操作失败，并给出警告弹窗
     * @param integer $codeB 正式响应码
     * @return void
     */
    public function response($data, $msg = "操作", $codeA = 0, $codeB = 200)
    {
        if ($data) {
            return response()->json([
                'code' => $codeA, 'data' => $data, 'msg' => $msg . "成功"
            ], $codeB);
        } else {
            return response()->json([
                'code' => 1, 'data' => '', 'msg' => $msg . '失败'
            ], $codeB);
        }
    }


    /**
     * 数组的键转驼峰后数组转树
     *
     * @param array $arr
     * @param string $root
     * @param string $parentId
     * @return array
     */
    public function toHumpTree($arr, $root = 'id', $parentId = 'parentId')
    {
        $root = Str::camel($root);
        $parentId = Str::camel($parentId);
        $ar = $this->toTree($this->toHump($arr), 0, $root, $parentId);
        return $ar;
    }

    /**
     * 数组转饿了么ui表格树状结构
     *
     * @param [type] $list array
     * @param integer $pid 父级的id值 初始pid
     * @param string $root 数据库父级字段
     * @param string $parentId 数据库外键字段
     * @return void
     */
    public $ar = array();
    public function toTree($list, $pid = 0, $root = 'id', $parentId = 'parentId')
    {
        $arr = array();
        foreach ($list as $k => $v) {
            if ($v[$parentId] == $pid) {
                array_push($this->ar, $v[$root]);
                $v['children'] = $this->toTree($list, $v[$root], $root, $parentId);
                $arr[] = $v;
                unset($list[$k]);
            }
        }
        return $arr;
    }

    public function toTreeids($i)
    {
        $this->toTree($i);
        return $this->ar;
    }

    /**
     * 数组的键转驼峰
     *
     * @param [type] $arr
     * @return array
     */
    public function toHump($arr)
    {
        $ar = [];
        foreach ($arr as $k => $v) {
            foreach ($v as $kk => $vv) {
                $ar[$k][Str::camel($kk)] = $vv;
            }
        }
        return $ar;
    }

    /**
     * 数组驼峰转下划线
     *
     * @param [type] $arr
     * @return void
     */
    public function toLine($arr)
    {
        $ar = [];
        foreach ($arr as $k => $v) {
            foreach ($v as $kk => $vv) {
                $ar[$k][Str::snake($kk)] = $vv;
            }
        }
        return $ar;
    }

    public function menu_parameter($paramsters, $menu_table, $menu_id)
    {
        if (!empty($paramsters)) {
            MenuParameter::where([['menu_table', $menu_table], ['menu_id', $menu_id]])->delete();
            foreach ($paramsters as $key => $par) {
                $items[$key]['menu_table'] = $menu_table;
                $items[$key]['menu_id'] = $menu_id;
                foreach ($par as $k => $p) {
                    $items[$key][$k] = $p;
                }
            }
            foreach ($items as $k => $par) {
                MenuParameter::create($par);
            };
        } else {
            MenuParameter::where([['menu_table', $menu_table], ['menu_id', $menu_id]])->delete();
        }
    }
}
