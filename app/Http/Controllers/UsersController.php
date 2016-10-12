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
    //获取用户列表
    public function users(){

        $users = $this->wechat->user->lists();
        return $users;

    }
    //用户信息
    public function user($openId){

        $user = $this->wechat->user->get($openId);
        return $user;

    }

    public function remark(){

        $this->wechat->user->remark('oifXIv4d4mZUajPm6QQeWi6tfhYY','Wewen');

        return 'ok';
    }


}
