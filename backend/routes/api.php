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

Route::group(['namespace' => 'App\Http\Controllers\API', 'prefix' => 'auth', 'middleware'=>['cors']], function () {
    Route::post('login', 'AuthController@login')->name('login');
    Route::get('logout', 'AuthController@logout');
    Route::get('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me')->middleware(['jwt.role:user', 'jwt.auth']);
});


# 后台用户登录
Route::group(['namespace' => 'App\Http\Controllers', 'prefix' => 'admin'], function () {
    Route::post('login', 'AdminController@login')->name('login');
    Route::get('logout', 'AdminController@logout');
    Route::get('refresh', 'AdminController@refresh');
    Route::get('me', 'AdminController@me')->middleware(['jwt.role:admin', 'jwt.auth']);
});

// Optional: Disable authentication in development
//Route::group(['middleware' => ['api', 'cors']], function () {
Route::group(['middleware' => ['permission', 'cors', 'jwt.role:user', 'jwt.auth']], function () {
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

    Route::group(['namespace' => 'App\Http\Controllers\API'], function () {
        //  Historian Tag
        Route::prefix('historian-tag')->group(function () {
            Route::get('index', 'HistorianTagController@index');
            Route::get('all', 'HistorianTagController@all');
            Route::get('listdata', 'HistorianTagController@listWithData');
            Route::get('show/{id}', 'HistorianTagController@show');
            Route::get('load', 'HistorianTagController@load');
            Route::post('show-many', 'HistorianTagController@showMany');
            Route::post('store', 'HistorianTagController@store');
            Route::post('update/{id}', 'HistorianTagController@update');
            Route::delete('destroy/{id}', 'HistorianTagController@destroy');
        });

        // Historian Data
        Route::prefix('historian-data')->group(function () {
            Route::post('current-data', 'HistorianDataController@currentData');
            Route::post('raw-data', 'HistorianDataController@rawData');//原始数据
            Route::post('sampled-data', 'HistorianDataController@sampledData');
            Route::post('watch-data', 'HistorianDataController@watchData'); //监控数据
        });

        //用户组织
        Route::prefix('orgnizations')->group(function () {
            Route::get('tree', 'OrgnizationController@tree');
            Route::post('store', 'OrgnizationController@store'); //创建用户组织保存
            Route::get('{orgnization}/role', 'OrgnizationController@role');  //用户组织角色页   路由模型绑定
            Route::post('{orgnization}/role', 'OrgnizationController@storeRole'); //保存用户组织角色页   路由模型绑定
            Route::post('delete', 'OrgnizationController@delete');
        });

        //接口权限
        Route::prefix('api')->group(function () {
            Route::get('tree', 'ApiController@tree');
            Route::post('store', 'ApiController@store'); //创建用户组织保存
            Route::post('delete', 'ApiController@delete');
        });

        // IEC104取得的电表数据
        Route::prefix('electricity')->group(function () {
            Route::get('index', 'ElectricityController@index');
        });

        // 地磅上报数据接口
        Route::prefix('weighbridge')->group(function () {
            Route::get('index', 'WeighBridgeController@index');
        });

        //API 资源路由
        Route::apiResources([
            'dcs-standard' => DcsStandardController::class,
        ]);
    });
});

Route::group(['namespace' => 'App\Http\Controllers\API'], function () {
    // IEC104取得的电表数据
    Route::prefix('electricity')->group(function () {
        Route::post('store_multi', 'ElectricityController@store_multi');
    });

    // 地磅上报数据接口
    Route::prefix('weighbridge')->group(function () {
        Route::post('store_multi', 'WeighBridgeController@store_multi');
    });
});

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::get('member/lists', 'MemberController@lists');
    Route::get('member/{member}/info', 'MemberController@info');
});
