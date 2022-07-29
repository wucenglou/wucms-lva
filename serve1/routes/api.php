<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\AuthorityController;
use App\Http\Controllers\Api\CasbinController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ModeController;
use App\Http\Controllers\Api\CatMenuController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\SearchController;

use App\Http\Controllers\Common\FileController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// 导入对应产品模块的client
use TencentCloud\Sms\V20210111\SmsClient;
// 导入要请求接口对应的Request类
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Credential;
// 导入可选配置类
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;

Route::get('test2', [SearchController::class, 'index']);
Route::get('test', function () {
    try {
        /* 必要步骤：
     * 实例化一个认证对象，入参需要传入腾讯云账户密钥对secretId，secretKey。
     * 这里采用的是从环境变量读取的方式，需要在环境变量中先设置这两个值。
     * 你也可以直接在代码中写死密钥对，但是小心不要将代码复制、上传或者分享给他人，
     * 以免泄露密钥对危及你的财产安全。
     * SecretId、SecretKey 查询: https://console.cloud.tencent.com/cam/capi */
        $cred = new Credential(env("TENCENTCLOUD_SECRET_ID"), env("TENCENTCLOUD_SECRET_KEY"));
        //$cred = new Credential(getenv("TENCENTCLOUD_SECRET_ID"), getenv("TENCENTCLOUD_SECRET_KEY"));

        // 实例化一个http选项，可选的，没有特殊需求可以跳过
        $httpProfile = new HttpProfile();
        // 配置代理（无需要直接忽略）
        // $httpProfile->setProxy("https://ip:port");
        $httpProfile->setReqMethod("GET");  // post请求(默认为post请求)
        $httpProfile->setReqTimeout(30);    // 请求超时时间，单位为秒(默认60秒)
        $httpProfile->setEndpoint("sms.tencentcloudapi.com");  // 指定接入地域域名(默认就近接入)

        // 实例化一个client选项，可选的，没有特殊需求可以跳过
        $clientProfile = new ClientProfile();
        $clientProfile->setSignMethod("TC3-HMAC-SHA256");  // 指定签名算法(默认为HmacSHA256)
        $clientProfile->setHttpProfile($httpProfile);

        // 实例化要请求产品(以sms为例)的client对象,clientProfile是可选的
        // 第二个参数是地域信息，可以直接填写字符串ap-guangzhou，支持的地域列表参考 https://cloud.tencent.com/document/api/382/52071#.E5.9C.B0.E5.9F.9F.E5.88.97.E8.A1.A8
        $client = new SmsClient($cred, "ap-guangzhou", $clientProfile);

        // 实例化一个 sms 发送短信请求对象,每个接口都会对应一个request对象。
        $req = new SendSmsRequest();

        /* 填充请求参数,这里request对象的成员变量即对应接口的入参
     * 你可以通过官网接口文档或跳转到request对象的定义处查看请求参数的定义
     * 基本类型的设置:
     * 帮助链接：
     * 短信控制台: https://console.cloud.tencent.com/smsv2
     * 腾讯云短信小助手: https://cloud.tencent.com/document/product/382/3773#.E6.8A.80.E6.9C.AF.E4.BA.A4.E6.B5.81 */

        /* 短信应用ID: 短信SdkAppId在 [短信控制台] 添加应用后生成的实际SdkAppId，示例如1400006666 */
        // 应用 ID 可前往 [短信控制台](https://console.cloud.tencent.com/smsv2/app-manage) 查看
        $req->SmsSdkAppId = "1400430256";
        /* 短信签名内容: 使用 UTF-8 编码，必须填写已审核通过的签名 */
        // 签名信息可前往 [国内短信](https://console.cloud.tencent.com/smsv2/csms-sign) 或 [国际/港澳台短信](https://console.cloud.tencent.com/smsv2/isms-sign) 的签名管理查看
        $req->SignName = "全球兵器";
        /* 模板 ID: 必须填写已审核通过的模板 ID */
        // 模板 ID 可前往 [国内短信](https://console.cloud.tencent.com/smsv2/csms-template) 或 [国际/港澳台短信](https://console.cloud.tencent.com/smsv2/isms-template) 的正文模板管理查看
        $req->TemplateId = "731819";
        /* 模板参数: 模板参数的个数需要与 TemplateId 对应模板的变量个数保持一致，若无模板参数，则设置为空*/
        $req->TemplateParamSet = array("1234", '5');
        /* 下发手机号码，采用 E.164 标准，+[国家或地区码][手机号]
     * 示例如：+8613711112222， 其中前面有一个+号 ，86为国家码，13711112222为手机号，最多不要超过200个手机号*/
        $req->PhoneNumberSet = array("15517303238");
        /* 用户的 session 内容（无需要可忽略）: 可以携带用户侧 ID 等上下文信息，server 会原样返回 */
        $req->SessionContext = "";
        /* 短信码号扩展号（无需要可忽略）: 默认未开通，如需开通请联系 [腾讯云短信小助手] */
        $req->ExtendCode = "";
        /* 国际/港澳台短信 SenderId（无需要可忽略）: 国内短信填空，默认未开通，如需开通请联系 [腾讯云短信小助手] */
        $req->SenderId = "";

        // 通过client对象调用SendSms方法发起请求。注意请求方法名与请求对象是对应的
        // 返回的resp是一个SendSmsResponse类的实例，与请求对象对应
        $resp = $client->SendSms($req);

        // 输出json格式的字符串回包
        print_r($resp->toJsonString());

        // 也可以取出单个值。
        // 您可以通过官网接口文档或跳转到response对象的定义处查看返回字段的定义
        // print_r($resp->TotalCount);

        /* 当出现以下错误码时，快速解决方案参考
     * [FailedOperation.SignatureIncorrectOrUnapproved](https://cloud.tencent.com/document/product/382/9558#.E7.9F.AD.E4.BF.A1.E5.8F.91.E9.80.81.E6.8F.90.E7.A4.BA.EF.BC.9Afailedoperation.signatureincorrectorunapproved-.E5.A6.82.E4.BD.95.E5.A4.84.E7.90.86.EF.BC.9F)
     * [FailedOperation.TemplateIncorrectOrUnapproved](https://cloud.tencent.com/document/product/382/9558#.E7.9F.AD.E4.BF.A1.E5.8F.91.E9.80.81.E6.8F.90.E7.A4.BA.EF.BC.9Afailedoperation.templateincorrectorunapproved-.E5.A6.82.E4.BD.95.E5.A4.84.E7.90.86.EF.BC.9F)
     * [UnauthorizedOperation.SmsSdkAppIdVerifyFail](https://cloud.tencent.com/document/product/382/9558#.E7.9F.AD.E4.BF.A1.E5.8F.91.E9.80.81.E6.8F.90.E7.A4.BA.EF.BC.9Aunauthorizedoperation.smssdkappidverifyfail-.E5.A6.82.E4.BD.95.E5.A4.84.E7.90.86.EF.BC.9F)
     * [UnsupportedOperation.ContainDomesticAndInternationalPhoneNumber](https://cloud.tencent.com/document/product/382/9558#.E7.9F.AD.E4.BF.A1.E5.8F.91.E9.80.81.E6.8F.90.E7.A4.BA.EF.BC.9Aunsupportedoperation.containdomesticandinternationalphonenumber-.E5.A6.82.E4.BD.95.E5.A4.84.E7.90.86.EF.BC.9F)
     * 更多错误，可咨询[腾讯云助手](https://tccc.qcloud.com/web/im/index.html#/chat?webAppId=8fa15978f85cb41f7e2ea36920cb3ae1&title=Sms)
     */
    } catch (TencentCloudSDKException $e) {
        echo $e;
    }
});

Route::prefix('admin')->group(function () {
});

// 模型
Route::post('mode', [ModeController::class, 'addmode']);
Route::get('mode', [ModeController::class, 'getmode']);
Route::match(['get', 'put', 'delete'], 'modeById', [ModeController::class, 'modeById']);
Route::get('modeList', [ModeController::class, 'modeList']);

// 栏目
Route::get('catList', [CatMenuController::class, 'catList']);
Route::match(['post', 'put'], 'catmenu', [CatMenuController::class, 'catmenu']);
Route::delete('catmenu', [CatMenuController::class, 'deletecatmenu']);

// 提交
Route::post('post', [PostController::class, 'store']);
Route::post('getpost', [PostController::class, 'getPost']);
Route::delete('post', [PostController::class, 'deletePost']);

Route::post('optionspost', [PostController::class, 'optionsPost']);

// 查询
Route::post('posts', [PostController::class, 'getPosts']);

Route::prefix('base')->group(function () {
    Route::match(['get', 'post'], 'captcha', [BaseController::class, 'captcha']);
    Route::post('login', [UserController::class, 'login']);
});

// 评论管理
Route::post('comment', [CommentController::class, 'getComments']);



// 文件仓库
Route::post('uploadimgs', [FileController::class, 'uploadimgs']);

// 权限控制
Route::prefix('casbin')->group(function () {
    Route::post('updateCasbin', [CasbinController::class, 'updateCasbin']);
    Route::post('getPolicyPath', [CasbinController::class, 'getPolicyPath'])->middleware('casbin');
});
Route::prefix('api')->group(function () {
    Route::post('initAddApi', [ApiController::class, 'initAddApi']);
    Route::post('getAllApis', [ApiController::class, 'getAllApis']);
    Route::post('getApiList', [ApiController::class, 'getApiList']);
});
Route::prefix('user')->group(function () {
    // Route::post('login', [UserController::class, 'login']);
    Route::post('t', [UserController::class, 't']);
    Route::post('signup', [UserController::class, 'signup']);
    Route::post('register', [UserController::class, 'signup']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('getUserInfo', [UserController::class, 'getUserInfo']);
    Route::post('getUserList', [UserController::class, 'getUserList']);
    Route::delete('deleteUser', [UserController::class, 'deleteUser']);
    Route::put('setUserInfo', [UserController::class, 'setUserInfo']);
    Route::post('resetPassword', [UserController::class, 'resetPassword']);
    Route::post('setUserAuthorities', [UserController::class, 'setUserAuthorities'])->middleware('casbin');
    Route::post('setUserAuthority', [UserController::class, 'setUserAuthority']);
    Route::post('option', [UserController::class, 'option']);
    Route::post('loginlog', [UserController::class, 'log']);
});
Route::prefix('authority')->group(function () {
    Route::post('getAuthorityList', [AuthorityController::class, 'getAuthorityList']);
    Route::post('createAuthority', [AuthorityController::class, 'createAuthority']);
    Route::post('deleteAuthority', [AuthorityController::class, 'deleteAuthority']);
    Route::put('updateAuthority', [AuthorityController::class, 'updateAuthority']);
});
Route::any('t', function () {
    dd(Auth::guest());
});
Route::prefix('menu')->group(function () {
    Route::post('getMenu', [MenuController::class, 'getMenu']);
    Route::post('getMenuList', [MenuController::class, 'getMenuList']);
    Route::post('addBaseMenu', [MenuController::class, 'addBaseMenu']);
    Route::post('getBaseMenuById', [MenuController::class, 'getBaseMenuById']);
    Route::post('deleteBaseMenu', [MenuController::class, 'deleteBaseMenu']);
    Route::post('updateBaseMenu', [MenuController::class, 'updateBaseMenu']);
    Route::post('getBaseMenuTree', [MenuController::class, 'getMenu']);
    Route::post('getMenuAuthority', [MenuController::class, 'getMenuAuthority']);
    Route::post('addMenuAuthority', [MenuController::class, 'addMenuAuthority']);
});

Route::post('casbin/getPolicyPathByAuthorityId', []);




// Route::group(['prefix' => 'auth'], function () {
//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('signup', [AuthController::class, 'signup']);

//     Route::group(['middleware' => 'auth:api'], function () {
//         Route::get('logout', [AuthController::class, 'logout']);
//         Route::get('user', [AuthController::class, 'user']);
//     });
// });
