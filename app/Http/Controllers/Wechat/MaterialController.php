<?php

namespace App\Http\Controllers\Wechat;

use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * 素材管理
 */
class MaterialController extends Controller
{
    public $material;

    public function __construct(Application $material)
    {
        $this->material = $material;
    }

    //上传图片
    public function image(){
        $img = $this->material->material->uploadImage(public_path().'/images/icon.png');
        return $img;
    }

    public function video(){

    }
    public function voice(){

    }

}
