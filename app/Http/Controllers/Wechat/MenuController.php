<?php

namespace App\Http\Controllers\Wechat;

use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * 菜单管理
 */
class MenuController extends Controller
{
    public $menu;

    public function __construct(Application $menu){
        $this->menu = $menu->menu;
    }
    //获取普通菜单
    public function ordinaryMenu(){
        return $this->menu->all();
    }

    //获取自定义菜单
    public function customMenu(){
        return $this->menu->current();

    }
    //创建自定义菜单
    public function create(){
        $appid = env('WECHAT_APPID');
        $base_url = env('BASE_URL');
        $auth_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=http%3a%2f%2fmp.hackqy.com%2fwechat%2fmonitor%2fauth&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        $buttons = [
            [
                "name"       => "简介",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "关于作者",
                        "url"  => "http://hackqy.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "简介",
                        "url"  => "$base_url"
                    ],
                    [
                        "type" => "view",
                        "name" => "加入我们",
                        "url"  => "https://github.com/wechat-laravel"
                    ],
                ],
            ],
            [
                "name"       => "管理系统",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "采集",
                        "url"  => "$base_url"
                    ],
                    [
                        "type" => "view",
                        "name" => "分类",
                        "url"  => "$base_url"
                    ],
                    [
                        "type" => "click",
                        "name" => "系统入口",
                        "key" => "V1001_GOOD"
                    ],
                ],
            ],
            [
                "name"       => "监控系统",
                "sub_button" => [
                    [
                        "type" => "click",
                        "name" => "监控 IP",
                        "key"  => "IP_status"
                    ],
                    [
                        "type" => "click",
                        "name" => "监控速度",
                        "key" => "Pyspider_rate"
                    ],
                    [
                        "type" => "view",
                        "name" => "监控后台",
                        "url"  => $auth_url
                    ],
                ],
            ],
        ];

        return $this->menu->add($buttons);

    }



}
