<?php

namespace App\Http\Controllers\Wechat;

use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * 用户相关操作
 */

class UsersController extends Controller
{
    public $wechat;

    //使用容器的自动注入,Application使用的事EasyWeChat的容器
    public function __construct(Application $wechat){
        $this->wechat = $wechat;
    }
    //用户列表
    public function userList(){

        $users = $this->wechat->user->lists();
        return $users;

    }
    //用户信息
    public function userInfo($openId){

        $user = $this->wechat->user->get($openId);
        return $user;

    }
    //修改备注
    public function remark($openId,$remark){

        $res = $this->wechat->user->remark($openId,$remark);
        return $res;
    }

    //分组列表
    public function groupList(){

        return  $this->wechat->user_group->lists();

    }

    //创建分组
    public function groupCreate($name){

        return $this->wechat->user_group->create($name);

    }

    //修改分组
    public function groupUpdate($groupId,$name){

        return $this->wechat->user_group->update($groupId,$name);

    }
    //删除分组
    public function groupDelete($groupId){

        return $this->wechat->user_group->delete($groupId);

    }
    //移动单个用户到指定分组
    public function groupMove($openId,$groupId){

        return $this->wechat->user_group->moveUser($openId,$groupId);

    }
    //批量移动用户到指定分组
    public function groupMoves(array $openIds,$groupId){

        return $this->wechat->user_group->moveUsers($openIds, $groupId);

    }
    //获取标签列表
    public function tagList(){

        return $this->wechat->user_tag->lists();

    }
    //获取标签下粉丝列表(其实就是分组下的粉丝列表)
    public function tagUsers($tagId,$nextOpenId=''){
        // $nextOpenId：第一个拉取的OPENID，不填默认从头开始拉取
        return $this->wechat->user_tag->usersOfTag($tagId,$nextOpenId);
    }

}
