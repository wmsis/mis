<?php

use Illuminate\Support\Facades\Route;
use App\Events\TaskFlowEvent;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HistorianController@index')->name('login');

Route::any('wechat/main', 'WechatController@main');
Route::get('wxpay/product', 'WxpayController@product');
Route::get('wxpay/notify', 'WxpayController@notify');
Route::post('wxpay/notify', 'WxpayController@notify');
Route::post('wxpay/prepay', 'WxpayController@prepay');
Route::get('flush', 'WechatController@ilovethisgame');


//Historian
Route::get('historian/tags', 'HistorianController@tags');
Route::get('historian/tagslist', 'HistorianController@tagslist');

Route::get('phpinfo', 'WechatController@phpinfo');

//broadcast 触发广播，并没有发通知
//Route::get('broadcast', function(){
    //broadcast(new TaskFlowEvent());
    //event( new TaskFlowEvent());
//});
