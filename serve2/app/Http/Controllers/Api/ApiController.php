<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    //
    public function initAddApi()
    {
        $routes = Route::getRoutes()->get(); // Illuminate\Routing\Route
        // $name = Route::currentRouteName(); // string
        // $action = Route::currentRouteAction(); // string
        foreach ($routes as $k => $value) {
            // if (Str::of($value->uri)->contains('api') && !(Str::of($value->uri)->contains(['telescope']))) {
            if (!(Str::of($value->uri)->contains(['telescope', 'ignition', 'sanctum', 'oauth']))) {
                $arr[$k]['path'] = Str::of('/' . $value->uri)->before('/{');
                $arr[$k]['methods'] = implode('-',array_diff($value->methods, ['HEAD']));
                $arr[$k]['api_group'] = $value->action['prefix'] ?? Str::of('/' . $value->uri)->before('/{');
                // $arr[$k]['description'] = Str::of($arr[$k]['path'])->basename();
            }
        }
        
        $a = Api::upsert($arr, ['path'] , ['methods','api_group']);
        return $a;
    }

    public function getApiList()
    {
        $res = Api::all();
        return response()->json([
            'code' => 0, 'data' => [
                'list' => $this->toHump($res->toArray()),
                'page' => 1,
                'pageSize' => 10,
                'total' => 22
            ], 'msg' => "切换成功"
        ]);
    }

    public function getAllApis()
    {
        $res = Api::all();
        return response()->json([
            'code' => 0, 'data' => [
                'apis' => $this->toHump($res->toArray()),
                'page' => 1,
                'pageSize' => 10,
                'total' => 22
            ], 'msg' => "切换成功"
        ]);
    }

}
