<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WechatController;
use App\Http\Controllers\HistorianController;

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

Route::get('/', function () {
    return view('welcome');
});


Route::any('wechat/main', [WechatController::class, 'main']);
Route::get('flush', [WechatController::class, 'ilovethisgame']);


//Historian
Route::get('historian/tags', [HistorianController::class, 'tags']);
Route::get('historian/tagslist', [HistorianController::class, 'tagslist']);

Route::get('phpinfo', [WechatController::class, 'phpinfo']);
