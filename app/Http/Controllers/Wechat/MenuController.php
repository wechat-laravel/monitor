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

    //
    public function menus(){

    }


}
