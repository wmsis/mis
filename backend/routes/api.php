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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
    //return $request->user();
//});

Route::group(['namespace' => 'API', 'middleware'=>['cors']], function () {
    Route::post('auth/login', 'AuthController@login');
});

Route::group(['namespace' => 'API', 'middleware'=>['cors', 'api']], function () {
    Route::get('auth/logout', 'AuthController@logout');
    Route::get('auth/refresh', 'AuthController@refresh');
});

// Optional: Disable authentication in development
//Route::group(['middleware' => ['api', 'cors']], function () {
Route::group(['middleware' => ['api', 'permission', 'cors']], function () {
    //用户
    Route::get('admins', 'AdminController@index'); //用户列表页
    Route::post('admins/store', 'AdminController@store'); //创建用户保存
    Route::get('admins/{user}/role', 'AdminController@role');  //用户角色页   路由模型绑定
    Route::post('admins/{user}/role', 'AdminController@storeRole'); //保存用户角色页   路由模型绑定
    Route::post('admins/delete', 'AdminController@delete');
    Route::post('admins/batchdelete', 'AdminController@batchdelete');
    Route::post('admins/chgpwd', 'AdminController@chgpwd');
    Route::post('admins/resetpwd', 'AdminController@resetpwd');
    Route::get('admins/{user}/tag', 'AdminController@tag');  //用户tag页   路由模型绑定
    Route::post('admins/{user}/tag', 'AdminController@storeTag');
    Route::post('admins/bind-member', 'AdminController@bindMember');

    //角色
    Route::get('roles', 'RoleController@index');   //列表展示页面
    Route::post('roles/store', 'RoleController@store'); //创建提交页面
    Route::get('roles/{role}/permission', 'RoleController@permission'); //角色权限页面  路由模型绑定
    Route::post('roles/{role}/permission', 'RoleController@storePermission'); //角色权限提交页面  路由模型绑定
    Route::post('roles/delete', 'RoleController@delete');
    Route::get('roles/lists', 'RoleController@lists');

    //权限
    Route::get('permissions', 'PermissionController@index');
    Route::get('permissions/all', 'PermissionController@all');
    Route::post('permissions/insert', 'PermissionController@insert');
    Route::post('permissions/update', 'PermissionController@update');
    Route::post('permissions/delete', 'PermissionController@delete');
    Route::get('permissions/children', 'PermissionController@children');


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

    //  Historian Tag
    Route::prefix('historian-tag')->group(function () {
        Route::get('index', 'API\HistorianTagController@index');
        Route::get('all', 'API\HistorianTagController@all');
        Route::get('listdata', 'API\HistorianTagController@listWithData');
        Route::get('show/{id}', 'API\HistorianTagController@show');
        Route::get('load', 'API\HistorianTagController@load');
        Route::get('remember/index', 'API\HistorianTagController@indexRememberTags');
        Route::post('show-many', 'API\HistorianTagController@showMany');
        Route::post('store', 'API\HistorianTagController@store');
        Route::post('bind-module', 'API\HistorianTagController@bindModule');
        Route::post('bind-group', 'API\HistorianTagController@bindGroup');
        Route::post('update/{id}', 'API\HistorianTagController@update');
        Route::post('remember/store', 'API\HistorianTagController@storeRememberTags');
        Route::delete('destroy/{id}', 'API\HistorianTagController@destroy');
        Route::get('user', 'API\HistorianTagController@userTags');
    });

    // Historian Module
    Route::prefix('historian-module')->group(function () {
        Route::get('index', 'API\HistorianModuleController@index');
        Route::get('show/{id}', 'API\HistorianModuleController@show');
        Route::post('store', 'API\HistorianModuleController@store');
        Route::post('update/{id}', 'API\HistorianModuleController@update');
        Route::delete('destroy/{id}', 'API\HistorianModuleController@destroy');
    });

    // Historian Data
    Route::prefix('historian-data')->group(function () {
        Route::post('current-data', 'API\HistorianDataController@currentData');
        Route::post('raw-data', 'API\HistorianDataController@rawData');
        Route::post('sampled-data', 'API\HistorianDataController@sampledData');
        Route::post('watch-data', 'API\HistorianDataController@watchData');
    });

    // Tag Group
    Route::prefix('tag-group')->group(function () {
        Route::get('index', 'API\TagGroupController@index');
        Route::get('show/{id}', 'API\TagGroupController@show');
        Route::post('store', 'API\TagGroupController@store');
        Route::post('update/{id}', 'API\TagGroupController@update');
        Route::delete('destroy/{id}', 'API\TagGroupController@destroy');
    });

    // Equipment
    Route::prefix('equipment')->group(function () {
        Route::get('index', 'API\Equipment\EquipmentController@index');
        Route::get('show/{id}', 'API\Equipment\EquipmentController@show');
        Route::post('store', 'API\Equipment\EquipmentController@store');
        Route::post('update/{id}', 'API\Equipment\EquipmentController@update');
        Route::delete('destroy/{id}', 'API\Equipment\EquipmentController@destroy');
        Route::get('run-stop-statistic', 'API\Equipment\EquipmentController@runStopStatistic');
        Route::get('run-stop-detail', 'API\Equipment\EquipmentController@runStopDetail');

        Route::prefix('{equipment_id}/param')->group(function () {
            Route::get('index', 'API\Equipment\EquipmentParamController@index');
            Route::get('show/{id}', 'API\Equipment\EquipmentParamController@show');
            Route::post('store', 'API\Equipment\EquipmentParamController@store');
            Route::post('update/{id}', 'API\Equipment\EquipmentParamController@update');
            Route::delete('destroy/{id}', 'API\Equipment\EquipmentParamController@destroy');
        });

        Route::prefix('{equipment_id}/change-record')->group(function () {
            Route::get('index', 'API\Equipment\EquipmentChangeRecordController@index');
            Route::get('show/{id}', 'API\Equipment\EquipmentChangeRecordController@show');
            Route::post('store', 'API\Equipment\EquipmentChangeRecordController@store');
            Route::post('update/{id}', 'API\Equipment\EquipmentChangeRecordController@update');
            Route::delete('destroy/{id}', 'API\Equipment\EquipmentChangeRecordController@destroy');
        });

        Route::prefix('{equipment_id}/spare-part')->group(function () {
            Route::get('index', 'API\Equipment\EquipmentSparePartController@index');
            Route::get('show/{id}', 'API\Equipment\EquipmentSparePartController@show');
            Route::post('store', 'API\Equipment\EquipmentSparePartController@store');
            Route::post('update/{id}', 'API\Equipment\EquipmentSparePartController@update');
            Route::delete('destroy/{id}', 'API\Equipment\EquipmentSparePartController@destroy');
        });

        Route::prefix('{equipment_id}/maintenance-record')->group(function () {
            Route::get('index', 'API\Equipment\EquipmentMaintenanceRecordController@index');
            Route::get('show/{id}', 'API\Equipment\EquipmentMaintenanceRecordController@show');
            Route::post('store', 'API\Equipment\EquipmentMaintenanceRecordController@store');
            Route::post('update/{id}', 'API\Equipment\EquipmentMaintenanceRecordController@update');
            Route::delete('destroy/{id}', 'API\Equipment\EquipmentMaintenanceRecordController@destroy');
        });
        Route::get('maintenance-record/gragh', 'API\Equipment\EquipmentMaintenanceRecordController@gragh');
    });

    // IEC104取得的电表数据
    Route::prefix('electricity')->group(function () {
        Route::get('index', 'API\ElectricityController@index');
        Route::get('show/{id}', 'API\ElectricityController@show');
        Route::delete('destroy/{id}', 'API\ElectricityController@destroy');
    });
});

// IEC104取得的电表数据
Route::prefix('electricity')->group(function () {
    Route::post('store', 'API\ElectricityController@store');
    Route::post('store_multi', 'API\ElectricityController@store_multi');
});


Route::get('member/lists', 'MemberController@lists');
Route::get('member/{member}/info', 'MemberController@info');
