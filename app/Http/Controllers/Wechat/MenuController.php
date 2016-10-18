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
    public function menu(){
        return $this->menu->all();
    }

    //获取自定义菜单
    public function menus(){
        return $this->menu->current();

    }
    //创建自定义菜单
    public function add(){
        
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
                        "url"  => "http://hackqy.com/"
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
                        "url"  => "http://mp.hackqy.com/user"
                    ],
                    [
                        "type" => "view",
                        "name" => "分类",
                        "url"  => "http://hackqy.com/"
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
                        "url"  => config('TEST_OAUTH')
                    ],
                ],
            ],
        ];

        return $this->menu->add($buttons);

    }



}
