<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Enforcer;
use Illuminate\Support\Facades\Auth;

class CasbinMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next,$userGuard = 'api', $casbinGuard = '')
    {
        $Power_req = request()->server();
        $Power_user = Auth::guard($userGuard)->user();
        // if (! Enforcer::enforce($Power_user['authority_id'], $Power_req['REQUEST_URI'], $Power_req['REQUEST_METHOD'])) {
        //     return response()->json([
        //         'code' => 403, 'data' => '', 'msg' => "权限不足"
        //     ]);
        // } else {
            return $next($request);
        // }
    }
}
