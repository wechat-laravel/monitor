<?php

namespace App\Http\Controllers\Monitor;

use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Wechat\UsersController;


class PyspiderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $old  = time()-300;
        $rate = \App\Models\PyspiderModel::where('updated_at','>',$old)->count();
        return $rate;
    }


    public function auth(){
          //$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx2e13dc930f0df2c0&redirect_uri=http%3a%2f%2fmp.hackqy.com%2fwechat%2fmonitor%2fauth&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
//        $config = [
//
//        ];
//        $app = new Application($config);
//        $oauth = $app->oauth;
//
//        $user = $oauth->user();
        return 'ok';


    }

    public function hello(){
        return view('user');
    }

}
