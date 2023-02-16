<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//SASS用户
Route::group(['namespace' => 'App\Http\Controllers\API', 'prefix' => 'auth', 'middleware'=>['cors']], function () {
    Route::post('login', 'AuthController@login')->name('login');
    Route::get('logout', 'AuthController@logout');
    Route::get('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me')->middleware(['jwt.role:user', 'jwt.auth']);
    Route::post('switch', 'AuthController@switch')->middleware(['jwt.role:user', 'jwt.auth']);
    Route::post('login-by-system', 'AuthController@loginBySystem');
});

////用户 角色 权限 微信  历史数据库  组织  接口权限 电表  地磅 地磅垃圾分类  DCS映射关系 标准DCS 电表映射关系 抓斗数据库配置 电表数据库配置 历史数据库配置
Route::group(['middleware' => ['permission', 'cors', 'jwt.role:user', 'jwt.auth', 'auth:api']], function () {
    //用户 角色 权限 微信
    Route::group(['namespace' => 'App\Http\Controllers'], function () {
        //用户
        Route::get('users', 'UserController@index'); //用户列表页
        Route::post('users/store', 'UserController@store'); //创建用户保存
        Route::get('users/{user}/role', 'UserController@role');  //用户角色页   路由模型绑定
        Route::post('users/{user}/role', 'UserController@storeRole'); //保存用户角色页   路由模型绑定
        Route::post('users/delete', 'UserController@delete');
        Route::post('users/batchdelete', 'UserController@batchdelete');
        Route::post('users/chgpwd', 'UserController@chgpwd');
        Route::post('users/resetpwd', 'UserController@resetpwd');
        Route::post('users/bind-member', 'UserController@bindMember');
        Route::get('users/{user}/orgnization', 'UserController@orgnization');  //用户组织页   路由模型绑定
        Route::post('users/{user}/orgnization', 'UserController@storeOrgnization'); //保存用户组织页   路由模型绑定

        //角色
        Route::get('roles', 'RoleController@index');   //列表展示页面
        Route::post('roles/store', 'RoleController@store'); //创建提交页面
        Route::get('roles/{role}/permission', 'RoleController@permission'); //角色菜单权限页面  路由模型绑定
        Route::post('roles/{role}/permission', 'RoleController@storePermission'); //角色菜单权限提交页面  路由模型绑定
        Route::post('roles/delete', 'RoleController@delete');
        Route::get('roles/lists', 'RoleController@lists');
        Route::get('roles/{role}/api', 'RoleController@api'); //角色接口权限页面  路由模型绑定
        Route::post('roles/{role}/api', 'RoleController@storeApi'); //角色接口权限提交页面  路由模型绑定

        //权限
        Route::get('permissions/tree', 'PermissionController@tree');
        Route::post('permissions/insert', 'PermissionController@insert');
        Route::post('permissions/update', 'PermissionController@update');
        Route::post('permissions/delete', 'PermissionController@delete');


        //微信推送
        Route::get('wechat/pictxtlist', 'WechatController@picTxtList');
        Route::get('wechat/pictxtlistall', 'WechatController@picTxtListAll');
        Route::post('wechat/storepictxt', 'WechatController@storePicTxt');
        Route::post('wechat/deletepictxt', 'WechatController@deletePicTxt');
        Route::post('wechat/upload', 'WechatController@upload');
        Route::post('wechat/storematerial', 'WechatController@storeMaterial');
        Route::get('wechat/pictxt/{pictxt}/materials', 'WechatController@picTxtMaterialList');
        Route::get('wechat/material/{material}/members', 'WechatController@materialMember');
        Route::post('wechat/storememberpictxt', 'WechatController@storeMemberPicTxt');
        Route::post('wechat/storeautoreply', 'WechatController@storeAutoReply');
        Route::post('wechat/deleteautoreply', 'WechatController@deleteAutoReply');
        Route::get('wechat/autoreply', 'WechatController@autoReply');

        Route::post('wechat/storekeyword', 'WechatController@storeKeyword');
        Route::get('wechat/keywords', 'WechatController@keywords');
        Route::get('wechat/keylists', 'WechatController@keylists');
        Route::post('wechat/deletematerial', 'WechatController@deleteMaterial');
        Route::get('wechat/material/{material}', 'WechatController@materialDetail');
        Route::get('wechat/pictxtqueue', 'WechatController@picTxtQueue');
        Route::post('wechat/deletepictxtqueue', 'WechatController@deletepictxtqueue');
        Route::post('wechat/pictxtqueue/batchdelete', 'WechatController@batchDeletePicTxtQueue');

        //微信菜单
        Route::get('wechat/menus', 'WechatController@menus');
        Route::post('wechat/insertmenu', 'WechatController@insertmenu');
        Route::post('wechat/updatemenu', 'WechatController@updatemenu');
        Route::post('wechat/deletemenu', 'WechatController@deletemenu');
        Route::get('wechat/menuchildren', 'WechatController@menuchildren');
        Route::get('wechat/publishmenu', 'WechatController@publishmenu');
        Route::get('wechat/qrcode', 'WechatController@qrcode');

        //小程序
        Route::get('mini/wxlogin', 'MiniController@wxlogin');
    });

    //历史数据库  组织  接口权限 电表  地磅 地磅垃圾分类  DCS映射关系 标准DCS 电表映射关系 抓斗数据库配置 电表数据库配置 历史数据库配置
    Route::group(['namespace' => 'App\Http\Controllers\API'], function () {
        //用户组织
        Route::prefix('orgnizations')->group(function () {
            Route::get('page', 'OrgnizationController@index');
            Route::get('factories', 'OrgnizationController@factories');
            Route::get('tree', 'OrgnizationController@tree');
            Route::post('store', 'OrgnizationController@store'); //创建用户组织保存
            Route::get('{orgnization}/role', 'OrgnizationController@role');  //用户组织角色页   路由模型绑定
            Route::post('{orgnization}/role', 'OrgnizationController@storeRole'); //保存用户组织角色页   路由模型绑定
            Route::post('delete', 'OrgnizationController@delete');
            Route::post('switch', 'OrgnizationController@switch');
        });

        //接口权限
        Route::prefix('api')->group(function () {
            Route::get('tree', 'ApiController@tree');
            Route::post('store', 'ApiController@store'); //创建用户组织保存
            Route::post('delete', 'ApiController@delete');
        });

        // IEC104取得的电表数据
        Route::prefix('electricity')->group(function () {
            Route::get('categories', 'ElectricityController@categories');
            Route::get('datalists', 'ElectricityController@datalists');
        });

        // 获取抓斗数据列表
        Route::prefix('garbage')->group(function () {
            Route::get('categories', 'GarbageController@categories');
            Route::get('datalists', 'GarbageController@datalists');
        });

        // 地磅上报数据接口
        Route::prefix('weighbridge')->group(function () {
            Route::get('categories', 'WeighBridgeController@categories');
            Route::get('datalists', 'WeighBridgeController@datalists');
        });

        // 标准dcs名称列表
        Route::prefix('dcs-standard')->group(function () {
            Route::get('lists', 'DcsStandardController@lists');
            Route::get('datalists', 'DcsStandardController@datalists');
        });

        //首页统计
        Route::prefix('home')->group(function () {
            Route::get('total', 'HomeController@total');
            Route::get('chart', 'HomeController@chart');
        });

        //大数据分析
        Route::prefix('data-analysis')->group(function () {
            Route::get('total', 'DataAnalysisController@total');
            Route::get('chart', 'DataAnalysisController@chart');
            Route::get('economy-daily', 'DataAnalysisController@economyDaily');
        });
    });

    Route::group(['namespace' => 'App\Http\Controllers\MIS'], function () {
        //设备
        Route::prefix('device')->group(function () {
            Route::get('lists', 'DeviceController@lists');
            Route::get('page', 'DeviceController@index');
            Route::get('tree', 'DeviceController@tree');
            Route::get('show/{id}', 'DeviceController@show');
            Route::post('store', 'DeviceController@store');
            Route::post('destroy/{id}', 'DeviceController@destroy');
            Route::post('upload', 'DeviceController@upload');
        });

        //设备属性模板
        Route::prefix('device-property-template')->group(function () {
            Route::get('lists', 'DevicePropertyTemplateController@lists');
            Route::get('page', 'DevicePropertyTemplateController@index');
            Route::get('tree', 'DevicePropertyTemplateController@tree');
            Route::get('show/{id}', 'DevicePropertyTemplateController@show');
            Route::post('store', 'DevicePropertyTemplateController@store'); //创建用户组织保存
            Route::post('destroy/{id}', 'DevicePropertyTemplateController@destroy');
        });

        //报警
        Route::prefix('alarm')->group(function () {
            Route::get('page', 'AlarmController@index');
            Route::post('confirm', 'AlarmController@confirm');
        });

        //通知
        Route::prefix('notice')->group(function () {
            Route::get('page', 'NoticeController@index');
        });

        Route::post('task/confirm', 'TaskController@confirm');
        //API 资源路由  DCS映射关系 标准DCS 电表映射关系 抓斗数据库配置 电表数据库配置 历史数据库配置
        Route::apiResources([
            'alarm-grade' => AlarmGradeController::class,
            'alarm-rule' => AlarmRuleController::class,
            'announcement' => AnnouncementController::class,
            'task' => TaskController::class,
            'inspect-rule' => InspectRuleController::class,
        ]);
    });

    Route::group(['namespace' => 'App\Http\Controllers\DATA'], function () {
        //大数据大屏
        Route::prefix('screen')->group(function () {
            Route::get('chart', 'ScreenController@chart');
            Route::get('boiler-temperature', 'ScreenController@boilerTemperature');
        });
    });
});

//微信会员
Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::get('member/lists', 'MemberController@lists');
    Route::get('member/{member}/info', 'MemberController@info');
});
