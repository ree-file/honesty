<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/supplier/{supplier_id}','SupplierController@index');
Route::get('/supplier/goods/{supplier_id}','SupplierController@goods');
Route::post('/supplier/{supplier_id}/buy','SupplierController@buy');
Route::post('/checkuser','UserController@checkuser()');
//上货端
Route::post('/supplier/operate/{supplier_id}','SupplierController@operate');
Route::post('/notify','NotifyController@store');
Route::post('/shippers','LoginController@users')->name('login');
Route::post('/login','LoginController@index');
Route::post('/supplier/sale/{supplier_id}','SupplierController@sale');
Route::get('/supplier/{supplier_id}/history','SupplierController@history');
Route::post('/supplier/{supplier_id}/deletehistory','SupplierController@deletehistory');
//图表接口
Route::post('chart','ChartController@index');
//导出excel接口
Route::get('excel/export','ExcelController@export');
//支付接口
Route::get('/alipay/notify','AlipayController@webNotify');
Route::get('/alipay/return','AlipayController@webReturn');
Route::any('/wechat', 'WeChatController@serve');
Route::group(['middleware' => ['web', 'wechat.oauth']], function () {
    Route::get('/user', function () {
        $user = session('wechat.oauth_user'); // 拿到授权用户资料

        dd($user);
    });
});
