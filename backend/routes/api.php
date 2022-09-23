<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\System\TenementController;
use App\Http\Controllers\System\OrgnizationController;

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
});

# 系统管理员
Route::group(['namespace' => 'App\Http\Controllers\System', 'prefix' => 'admin', 'middleware'=>['cors']], function () {
    Route::post('login', 'AdminController@login')->name('login');
    Route::get('logout', 'AdminController@logout');
    Route::get('refresh', 'AdminController@refresh');
    Route::get('me', 'AdminController@me')->middleware(['jwt.role:admin', 'jwt.auth']);
});

//系统租户和系统组织
Route::group(['middleware'=>['cors', 'jwt.role:admin', 'jwt.auth', 'auth:admin']], function () {
    //补充路由应在 Route::apiResources 方法之前定义
    Route::get('tenements/lists', [TenementController::class, 'lists']);
    Route::post('tenements/switch', [TenementController::class, 'switch']);
    //API 资源路由
    Route::apiResources([
        'tenements' => TenementController::class,
    ]);
});

////用户 角色 权限 微信  历史数据库  组织  接口权限 电表  地磅 地磅垃圾分类  DCS映射关系 标准DCS 电表映射关系 抓斗数据库配置 电表数据库配置 历史数据库配置
Route::group(['middleware' => ['permission', 'cors', 'jwt.role:admin', 'jwt.auth', 'auth:admin']], function () {
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
            Route::get('page', 'OrgnizationController@index');
            Route::get('factories', 'OrgnizationController@factories');
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

        // 获取抓斗数据列表
        Route::prefix('garbage')->group(function () {
            Route::get('index', 'GarbageController@index');
        });

        // 地磅上报数据接口
        Route::prefix('weighbridge')->group(function () {
            Route::get('index', 'WeighBridgeController@index');
        });

        //地磅垃圾分类
        Route::prefix('weighbridge-category')->group(function () {
            Route::get('lists-big', 'WeighbridgeCategoryController@listsBig');
            Route::get('page-big', 'WeighbridgeCategoryController@pageBig');
            Route::get('show-big/{id}', 'WeighbridgeCategoryController@showBig');
            Route::post('store-big', 'WeighbridgeCategoryController@storeBig');
            Route::post('update-big/{id}', 'WeighbridgeCategoryController@updateBig');
            Route::delete('destroy-big/{id}', 'WeighbridgeCategoryController@destroyBig');
            Route::get('page-small', 'WeighbridgeCategoryController@pageSmall');
            Route::get('show-relation/{id}', 'WeighbridgeCategoryController@showRelation');
            Route::post('bind-relation', 'WeighbridgeCategoryController@bindRelation');
        });

        //如有补充路由应在 Route::apiResources 方法之前定义
        Route::get('dcs-standard/lists', 'DcsStandardController@lists');
        Route::get('dcs-group/show-relation/{id}', 'DcsGroupController@showRelation');
        Route::post('dcs-group/bind-relation', 'DcsGroupController@bindRelation');
        //API 资源路由  DCS映射关系 标准DCS 电表映射关系 抓斗数据库配置 电表数据库配置 历史数据库配置
        Route::apiResources([
            'dcs-standard' => DcsStandardController::class,
            'dcs-map' => DcsMapController::class,
            'electricity-map' => ElectricityMapController::class,
            'garbage-db-config' => GarbageDbConfigController::class,
            'electricity-db-config' => ElectricityDbConfigController::class,
            'dcs-db-config' => DcsDbConfigController::class,
            'dcs-group' => DcsGroupController::class,
        ]);
    });
});

//保存取得的电表数据  地磅上报数据接口  地磅小分类上报数据接口
Route::group(['namespace' => 'App\Http\Controllers\API'], function () {
    // IEC104保存取得的电表数据
    Route::prefix('electricity')->group(function () {
        Route::post('store_multi', 'ElectricityController@store_multi');
    });

    // 地磅上报数据接口
    Route::prefix('weighbridge')->group(function () {
        Route::post('store_multi', 'WeighBridgeController@store_multi');
    });

    // 地磅小分类上报数据接口
    Route::prefix('weighbridge-category')->group(function () {
        Route::post('store-small-multi', 'WeighbridgeCategoryController@storeSmallMulti');
    });
});

//微信会员
Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::get('member/lists', 'MemberController@lists');
    Route::get('member/{member}/info', 'MemberController@info');
});
