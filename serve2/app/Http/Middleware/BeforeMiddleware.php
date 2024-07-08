<?php

namespace App\Http\Middleware;

use App\Models\LoginLog;
use Closure;
use Illuminate\Http\Request;
use Enforcer;
use Illuminate\Support\Facades\Auth;

use Jenssegers\Agent\Agent;
use Ip2Region;

class BeforeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // $Power_req = request()->server();
        // dd($request->userAgent());


        $Power_user = Auth::guard('api')->user();
        if ($Power_user && $Power_user['status'] == 0) {
            return response()->json([
                'code' => 0, 'data' => '', 'msg' => "账号已被封禁"
            ]);
        } else {
            return $next($request);
        }
    }
}
