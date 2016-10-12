<?php

namespace App\Http\Controllers;

use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public $wechat;

    //使用容器的自动注入,Application使用的事EasyWeChat的容器
    public function __construct(Application $wechat){
        $this->wechat = $wechat;
    }
    //用户列表
    public function users(){

        $users = $this->wechat->user->lists();
        return $users;

    }
    //用户信息
    public function user($openId){

        $user = $this->wechat->user->get($openId);
        return $user;

    }
    //修改备注
    public function remark($openId,$remark){

        $res = $this->wechat->user->remark($openId,$remark);
        return $res;
    }

    //分组列表
    public function groups(){

        return  $this->wechat->user_group->lists();

    }

    //创建分组
    public function crup($name){

        return $this->wechat->user_group->create($name);

    }

    //修改分组
    public function upup($groupId,$name){

        return $this->wechat->user_group->update($groupId,$name);

    }
    //删除分组
    public function deup($groupId){

        return $this->wechat->user_group->delete($groupId);

    }
    //移动单个用户到指定分组
    public function mvup($openId,$groupId){

        return $this->wechat->user_group->moveUser($openId,$groupId);

    }
    //批量移动用户到指定分组
    public function mvups(array $openIds,$groupId){

        return $this->wechat->user_group->moveUsers($openIds, $groupId);

    }



}
