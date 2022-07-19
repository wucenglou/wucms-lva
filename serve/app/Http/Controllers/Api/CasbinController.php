<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Enforcer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CasbinController extends Controller
{
    //
    public function updateCasbin(Request $request)
    {
        $res = request();
        // $user = Auth::guard('api')->user();
        foreach ($res['casbinInfos'] as $v) {
            $methods = explode('-', $v['method']);
            if (count($methods) > 1) {
                for ($i = 0; $i < count($methods); $i++) {
                    Enforcer::addPolicy($res['authorityId'], $v['path'], $methods[$i]);
                }
            } else {
                Enforcer::addPolicy($res['authorityId'], $v['path'], $v['method']);
            }
        }
        return response()->json([
            'code' => 0, 'data' => [], 'msg' => "添加成功"
        ]);
    }

    public function getPolicyPath()
    {
        $res = request();
        // $user = Auth::guard('api')->user();
        $res = Enforcer::getPermissionsForUser($res['authorityId']);

        if (!$res) {
            return response()->json([
                'code' => 0, 'data' => [
                    'paths' => '',
                ], 'msg' => "获取成功"
            ]);
        }
        foreach ($res as $k => $v) {
            $paths[$k]['path'] = $v[1];
            $paths[$k]['method'] = $v[2];
        }
        $paths = array_reverse($paths);
        $length = count($paths);
        for ($i = 0; $i < $length; $i++) {
            for ($n = 0; $n < $length; $n++) {
                if (($n != $i) && isset($paths[$n]) && isset($paths[$i])) {
                    if ($paths[$i]['path'] == $paths[$n]['path']) {
                        $paths[$i]['method'] = $paths[$i]['method'] . '-' . $paths[$n]['method'];
                        unset($paths[$n]);
                    }
                }
            }
        }
        return response()->json([
            'code' => 0, 'data' => [
                'paths' => array_values($paths),
            ], 'msg' => "获取成功"
        ]);
    }
}
