<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ScreenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //记录最后时间的前的24个小时 时间增量比值
    protected $time_section = [];
    //最终阅读量
    protected $end_read_num ;

    public function index()
    {
        $now = time();
        $res = DB::table(env('DB_SCREEN'))
            ->select('sn','begin_at','end_at')
            ->where('end_at','<',$now)
            ->where('days','=',1)
            ->get();
        return $this->screen($res);
    }

    //根据最终阅读量区分
    public function screen($obj)
    {
        foreach ($obj as $ob) {
            $res = DB::table(env('DB_SCREEN_RESULT'))
                ->select('sn', 'read_num', 'like_num', 'updated_at')
                ->where('sn', '=', $ob->sn)
                ->orderby('updated_at', 'desc')
                ->get();
            foreach ($res as $re) {
                if ($re->read_num >= 8000 && $re->read_num <= 15000) {
                    $this->end_read_num = $re->read_num;
                    $this->section($re->updated_at);
                    $this->times($res);
                    return $this->incr();
                } else {
                    break;
                }
            }
        }


    }

    //一个sn的所有监控记录
    public function times($obj){
        $end_num = end($obj)->read_num;
        foreach ($obj as $ob){
            foreach ($this->time_section as $k => $v){
                //该数组的num会依次填空,
                if(empty($v['num'])){
                    //时间是倒叙的,所以刚满足下列条件,表示这个时刻最大的值
                    if($ob->updated_at <= $v['time']){
                        $this->time_section[$k]['num'] = $ob->read_num;
                    }
                }
            }
        }
        $this->time_section[24]['num'] = $end_num;
        return $this->time_section;
    }

    //增量比值
    public function incr(){
        //增量
        $sum = $this->time_section[0]['num'] - $this->time_section[24]['num'];
        $incr = [];
        for ($i = 24;$i>0;$i--){
            $incr[] = ($this->time_section[$i-1]['num'] - $this->time_section[$i]['num'])/$sum;
        }
        var_dump($incr);
    }

    //根据最后一次时间
    public function section($timestamp){
//        date_default_timezone_set('Asia/Shanghai');
        for($i=0;$i<25;$i++){
            $this->time_section[] = ['time'=>$timestamp-(3600*$i),'num'=>""];
        }
    }


}
