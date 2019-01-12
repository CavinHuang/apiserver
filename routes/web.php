<?php

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


//Route::get('/', function () {
//  return redirect('/admin');
//  // return view('welcome');
//});


Route::get('/wechat_image', "HomeController@wechatImage");
Route::get('/test', 'TestController@index');
Route::get('/testview', 'TestController@test_view');
// wechat image show


Route::group(['prefix' => 'api', 'namespace' => 'Api'], function(){
    Route::any('router', 'RouterController@index');  // API 入口
});



// 验证码
Route::get('kit/captcha/{tmp}', 'KitController@captcha');

// 后台路由
Route::get('/admin', function () {
  return view('admin');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/test/badwords', 'TestController@create_badwords');

Route::get('/testredisset', 'TestController@set');
Route::get('/test_qrcode', 'TestController@qrcode');
Route::get('/test_wallpaper', 'TestController@wallpaper');
Route::get('/test_collect', 'TestController@collect');


route::get('/webmsg', 'TestController@webmsg');

route::get('/sender', 'TestController@test_sender');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/str', 'TestController@str');
