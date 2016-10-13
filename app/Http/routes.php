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
Route::get('/users','Wechat\UsersController@users');
//用户信息
Route::get('/user/{openId}','Wechat\UsersController@user');
//用户备注
Route::get('/remark/{openId}/name/{name}','Wechat\UsersController@remark');
//分组列表
Route::get('/groups','Wechat\UsersController@groups');
//创建分组
Route::get('/crup/{name}','Wechat\UsersController@crup');
//修改分组
Route::get('/upup/{groupId}/name/{name}','Wechat\UsersController@upup');
//删除分组
Route::get('/deup/{groupId}','Wechat\UsersController@deup');
//移动单个用户到指定分组
Route::get('/mvup/{opendId}/group/{groupId}','Wechat\UsersController@mvup');
//批量移动用户到指定分组
Route::get('/mvups/{opendIds}/group/{groupId}','Wechat\UsersController@mvups');
//标签列表
Route::get('/tags','Wechat\UsersController@tags');
//标签下粉丝列表(其实就是分组下的粉丝列表)
Route::get('/tag/{tagId}/next/{openId?}','Wechat\UsersController@tag');

