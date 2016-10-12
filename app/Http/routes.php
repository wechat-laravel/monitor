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
Route::get('/remark/{openId}/name/{name}','UsersController@remark');
//分组列表
Route::get('/groups','UserController@groups');
//创建分组
Route::get('/crup','UserController@crup');
//修改分组
Route::get('/upup/{groupId}/name/{name}','UserController@upup');
//删除分组
Route::get('/deup/{groupId}','UserController@deup');
//移动单个用户到指定分组
Route::get('/mvup/{opendId}/group/{groupId}','UserController@mvup');
//批量移动用户到指定分组
Route::get('/mvups/{opendIds}/group/{groupId}','UserController@mvups');
