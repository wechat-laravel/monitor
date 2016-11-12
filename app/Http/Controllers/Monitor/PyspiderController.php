<?php

namespace App\Http\Controllers\Monitor;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;



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


    public function auth(Request $request){
        $code     = $request->get('code');
        $appid    = env('WECHAT_APPID');
        $secret   = env('WECHAT_SECRET');
        $base_url = env('BASE_URL');
        $url      = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
        $data     = $this->curls($url);

        $token    = $data->access_token;
        $openid   = $data->openid;
        $url      = "https://api.weixin.qq.com/sns/userinfo?access_token=$token&openid=$openid&lang=zh_CN";
        $result   = $this->curls($url);

        
        return view('user',['user'=>$result,'url'=>$base_url]);


    }

    public function hello(){

        return view('user');
    }

    public function curls($url){
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = \GuzzleHttp\json_decode($data);
        return $data;
    }

    public function test(){
        return 'ok';
    }
    public function test2(){
        return 'ok';
    }
}
