<?php

namespace App\Http\Controllers\Monitor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IpStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $num = \App\Models\IpStatusModel::where('status','=',0)->count();
        return $num;
        

        //强迫症,打卡.该项目暂搁置,工作需要转coding.net 代码推送
        //地址:https://coding.net/u/hackqy
    }


}
