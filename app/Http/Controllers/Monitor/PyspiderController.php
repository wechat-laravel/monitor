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
