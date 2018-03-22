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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group(['prefix' => 'api', 'namespace' => 'Api'], function(){
//     Route::any('router', 'RouterController@index');  // API 入口
// });
Route::post('/login', 'Api\\UserController@login');
Route::post('/register', 'Api\\UserController@register');
Route::group(['middleware' => 'auth:api'], function(){
  Route::post('details', 'Api\\UserController@details');
  Route::post('saveuser', 'Api\\UserController@update_user');
  Route::post('applists', 'Api\\AppsController@app_lists');
  Route::post('upload', 'Api\\UploadController@upload');
  Route::post('saveapps', 'Api\\AppsController@save_apps');
  Route::post('deleteapps', 'Api\\AppsController@delete_apps');
  Route::get('/appcount', 'Api\\AppsController@countToday');
  Route::get('/applogs', "Api\\AppsController@appLogs");
});


