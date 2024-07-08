<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    
    public function Captcha()
    {
        $res = app('captcha')->create('default', true);
        return response()->json(['code' => 0, 'data' => [
            'captchaId' => $res['key'],
            'captchaLength' => 6,
            'picPath' => $res['img']
        ]], 200);
        return response()->json(['code' => 0, 'data' => [
            'captchaId' => '',
            'captchaLength' => 6,
            'picPath' => ''
        ]], 200);
    }

}
