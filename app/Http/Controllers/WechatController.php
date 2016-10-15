<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Monitor\PyspiderController;
use App\Http\Controllers\Monitor\IpStatusController;

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

        $wechat->server->setMessageHandler(function($message) use ($userApi){
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
                                return '可用IP数: '.$num;
                            }
                            break;
                    }

                    break;
                case 'text':
                    //如果接收到'加入Wewen组',把用户添加到该组里
                    if($message->Content === '加入Wewen组'){
                        return $message->FromUserName;
                    }

                    return '你好 '.$userApi->get($message->FromUserName)->nickname;

                    break;
                case 'image':
                    # 图片消息...
                    break;
                case 'voice':
                    # 语音消息...
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
