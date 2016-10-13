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
    public function add(){
        $buttons = [
            [
                "name"       => "简介",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "关于author",
                        "url"  => "http://hackqy.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "关于博客",
                        "url"  => "http://hackqy.com/"
                    ],
                    [
                        "type" => "click",
                        "name" => "赞一下我们",
                        "key" => "V1001_GOOD"
                    ],
                ],
            ],
            [
                "name"       => "管理系统",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "搜索",
                        "url"  => "http://hackqy.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "视频",
                        "url"  => "http://hackqy.com/"
                    ],
                    [
                        "type" => "click",
                        "name" => "赞一下我们",
                        "key" => "V1001_GOOD"
                    ],
                ],
            ],
            [
                "name"       => "监控系统",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "搜索",
                        "url"  => "http://hackqy.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "视频",
                        "url"  => "http://hackqy.com/"
                    ],
                    [
                        "type" => "click",
                        "name" => "pyspider速度",
                        "key" => "V1001_GOOD"
                    ],
                ],
            ],
        ];

        return $this->menu->add($buttons);

    }



}
