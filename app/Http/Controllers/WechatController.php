<?php

namespace App\Http\Controllers;

use EasyWeChat\Message\Image;
use EasyWeChat\Message\Voice;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Monitor\PyspiderController;
use App\Http\Controllers\Monitor\IpStatusController;
use App\Http\Controllers\Wechat\UsersController;

class WechatController extends Controller
{

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        //Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $wechat  = app('wechat');
        $userApi = $wechat->user;

        $wechat->server->setMessageHandler(function($message) use ($userApi,$wechat){
            switch ($message->MsgType) {
                case 'event':
                    //多种事件
                    switch ($message->Event){
                        case 'subscribe':
                                return '欢迎关注Monitor测试号!';
                            break;
                        case 'CLICK':
                            if ($message->EventKey == 'Pyspider_rate'){
                                $pyspider = new PyspiderController();
                                $rate = $pyspider->index();
                                return '当前速度为: '.$rate;
                            }elseif($message->EventKey == 'IP_status'){
                                $pyspider = new IpStatusController();
                                $num = $pyspider->index();
                                return '故障IP数: '.$num;
                            }
                            break;
                    }

                    break;
                case 'text':
                    //如果接收到'加入Wewen组',把用户添加到该组里
                    if($message->Content === '加入Wewen组'){
//                        $user   = new UsersController();
//                        $result = $user->gmove($message->FromUserName,101);
                        return $message->FromUserName;
                    }

                    return '你好 '.$userApi->get($message->FromUserName)->nickname;

                    break;
                case 'image':
                    # 图片消息...
                    //回复的图片内容
                    $image = new Image(['media_id'=>'-tf6Ss1cZfE5ntVAAtFeO58gIVBoMZ-MIlhP2imNGsw']);
                    //作为客服消息发送
                    $wechat->staff->message($image)->to($message->FromUserName)->send();
                    //上面不能使用return  要不然报错
                    return '';
                    break;
                case 'voice':
                    //$voice = new Voice(['media_id'=>'']);
                    $messag = '您想说什么?';
                    $wechat->staff->message($messag)->to($message->FromUserName)->send();

                    return '';
                    break;
                case 'video':
                    # 视频消息...
                    break;
                case 'location':
                    # 坐标消息...
                    break;
                case 'link':
                    # 链接消息...
                    break;
                // ... 其它消息
                default:
                    return "该功能暂未开放.";
                    break;
            }

        });

        //Log::info('return response.');

        return $wechat->server->serve();
    }

}
