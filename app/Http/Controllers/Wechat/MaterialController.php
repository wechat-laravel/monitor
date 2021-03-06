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
    //永久素材
    public $material;
    //临时素材
    public $temporary;
    //群发消息
    public $broadcast;

    public function __construct(Application $wechat)
    {
        $this->material  = $wechat->material;
        $this->temporary = $wechat->material_temporary;
        $this->broadcast = $wechat->broadcast;

    }

    /**
     * 永久素材列表
     * $type 素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
     * $offset 从全部素材的该偏移位置开始返回，可选，默认 0，0 表示从第一个素材 返回
     * $count 返回素材的数量，可选，默认 20, 取值在 1 到 20 之间
     */
    public function lists($type, $offset, $count){

        return $this->material->lists($type,$offset,$count);

    }

    //上传图片
    public function uploadImage(){
        $img = $this->material->uploadImage(public_path().'/backend/img/icon.png');
        return $img;
    }
    //删除永久素材
    public function delete($mediaId){
        return $this->material->delete($mediaId);
    }
    //音频
    public function audio(){

    }

    //群发消息
    public function message(){
        return $this->broadcast->sendText('大家好！欢迎使用 Wechat-laravel测试号。');
    }

}
