<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{


    public function __construct()
    {
        //控制器中使用中间件
        //并指定受限制的方法
        $this->middleware('test',['only'=>['index','test']]);
        //指定不受限制的方法
        $this->middleware('test',['except'=>['text']]);

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return 'index';
    }
    
}
