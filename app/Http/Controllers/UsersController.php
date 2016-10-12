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

    public function users(){
        //获取用户列表
        $users = $this->wechat->user->lists();
        return $users;
    }

    public function user($openId){
        //获取用户信息
        $user = $this->wechat->user->get($openId);
        return $user;

    }
}
