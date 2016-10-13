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
Route::get('/wechat/users','Wechat\UsersController@users');
//用户信息
Route::get('/wechat/user/{openId}','Wechat\UsersController@user');
//用户备注
Route::get('/wechat/remark/{openId}/name/{name}','Wechat\UsersController@remark');
//分组列表
Route::get('/wechat/groups','Wechat\UsersController@groups');
//创建分组
Route::get('/wechat/gcreate/{name}','Wechat\UsersController@gcreate');
//修改分组
Route::get('/wechat/gupdate/{groupId}/name/{name}','Wechat\UsersController@gupdate');
//删除分组
Route::get('/wechat/gdelete/{groupId}','Wechat\UsersController@gdelete');
//移动单个用户到指定分组
Route::get('/wechat/gmove/{opendId}/group/{groupId}','Wechat\UsersController@gmove');
//批量移动用户到指定分组
Route::get('/wechat/gmoves/{opendIds}/group/{groupId}','Wechat\UsersController@gmoves');
//标签列表
Route::get('/wechat/tags','Wechat\UsersController@tags');
//标签下粉丝列表(其实就是分组下的粉丝列表)
Route::get('/wechat/tag/{tagId}/next/{openId?}','Wechat\UsersController@tag');

//素材管理
//图片
Route::get('/wechat/image','Wechat\MaterialController@image');
//语音
Route::get('/wechat/voice','Wechat\MaterialController@voice');
//视频
Route::get('/wechat/video','Wechat\MaterialController@video');
//查询菜单
Route::get('/wechat/menu','Wechat\MenuController@menu');
//自定义菜单
Route::get('/wechat/menus','Wechat\MenuController@menus');
//菜单添加
Route::get('/wechat/madd','Wechat\MenuController@add');

//pyspider监控


