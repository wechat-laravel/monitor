<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::any('/wechat', 'WechatController@serve');
//用户列表
Route::get('/users','UsersController@users');
//用户信息
Route::get('/user/{openId}','UsersController@user');
//用户备注
Route::get('/remark','UsersController@remark');