<?php

namespace App\Http\Controllers\Backend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SectionController extends Controller
{

    //记录最后时间的前的24个小时时间  增量比值
    protected $time_section = [];
    //记录该监控所在的整点时间段内的  增量比值
    protected $time_hour    = [];

    //最终阅读量
    protected $end_read_num ;

    //文章的唯一标识
    protected $sn ;

    public function index()
    {
        $id  = $this->getId();
        $now = time();
        $res = DB::table(env('DB_SCREEN'))
            ->select('sn')
            ->where('end_at','<',$now)
            ->where('days','=',1)
            ->where('id','>',$id)
            ->offset(0)
            ->limit(200)
            ->get();
//        return $res;
        if(empty($res)){
            exit;
        }
        return $this->screen($res);
    }

    //获取起始ID
    public function getId(){
        $res = DB::table(env('DB_RATIO'))
            ->select('sn')
            ->orderby('id','desc')
            ->first();
        if(empty($res)){
            $id = 0;
            return $id;
        }
        $id = DB::table(env('DB_SCREEN'))
            ->select('id')
            ->where('sn','=',$res->sn)
            ->first();
        return $id->id;
    }

    //根据最终阅读量来筛选
    public function screen($obj)
    {
        foreach ($obj as $ob) {
            //$res一个sn的所有记录
            $res = DB::table(env('DB_SCREEN_RESULT'))
                ->select('sn', 'read_num', 'like_num', 'updated_at')
                ->where('sn', '=', $ob->sn)
                ->orderby('updated_at', 'desc')
                ->get();
            foreach ($res as $re) {
                //判断该sn是否已经存在表中,存在跳过
                $exists = DB::table(env('DB_RATIO'))
                    ->select('sn')
                    ->where('sn','=',$re->sn)
                    ->first();
                if(!empty($exists)) break;

                if ($re->read_num >= 8000 && $re->read_num < 100000) {
                    $this->end_read_num = $re->read_num;
                    $this->sn = $re->sn;
//                    $this->section($re->updated_at);
                    $this->hours($re->updated_at);
//                    $this->monitorSection($res);
                    $this->monitorHours($res);
                    $result = $this->hoursIncr();
                    if(!$result){
                        exit($re->sn.'有误');
                    }else{
                        break;
                    }
                }else{
                    break;
                }
            }
        }


    }

    //一个sn的分段记录(第一个小时,第二个小时....)
    public function monitorSection($obj){
        $end_num = end($obj)->read_num;
        foreach ($obj as $ob){
            foreach ($this->time_hour as $k => $v){
                //该数组的num会依次填空,注:empty(0)也为真
                if(empty($v['num'])){
                    //时间是倒叙的,所以刚满足下列条件,表示这个时刻最大的值
                    if($ob->updated_at <= $v['time']){
                        $this->time_hour[$k]['num'] = $ob->read_num;
                    }
                }
            }
        }
        $this->time_section[24]['num'] = $end_num;
    }

    //一个sn的整点记录(18:00,19:00.....)
    public function monitorHours($obj){
        foreach ($obj as $ob){
            foreach ($this->time_hour as $k => $v){
                //该数组的num会依次填空,注:empty(0)也为真
                if(empty($v['num'])){
                    //时间是倒叙的,所以刚满足下列条件,表示这个时刻最大的值
                    if($ob->updated_at <= $v['time']){
                        $this->time_hour[$k]['num'] = $ob->read_num;
                    }
                }
            }
        }
    }

    //分段的增量比值
    public function sectionIncr(){
        //增量
        $sum = $this->time_section[0]['num'] - $this->time_section[24]['num'];
        $incr = [];
        for ($i = 24;$i>0;$i--){
            $incr[] = ($this->time_section[$i-1]['num'] - $this->time_section[$i]['num'])/$sum;
        }
    }
    //整点的增量比值
    public function hoursIncr(){
        //增量
        $sum = $this->time_hour[0]['num'] - $this->time_hour[23]['num'];
        //开始计算比值的起始时间
        //时间都转换成24小时制的整数
        $incr [] = ['sn'=>$this->sn,'times'=> intval(substr(date('Y/m/d H:i:s',$this->time_hour[23]['time']),11,2)) ,'ratio'=>0 ];
        //计算比值总和,如果等于零,表示该监控记录有误
        $num = 0;
        for ($i = 23;$i>0;$i--){
            $time = date('Y/m/d H:i:s',$this->time_hour[$i-1]['time']);
            $time = intval(substr($time,11,2));
            //ratio 比值 向上保留万分数 除以100
            if($this->time_hour[$i-1]['num'] - $this->time_hour[$i]['num'] != 0){
                $ratio=intval((ceil((($this->time_hour[$i-1]['num'] - $this->time_hour[$i]['num'])/$sum)*10000)));
            }else{
                $ratio=0;
            }
            $incr[] = ['sn'=>$this->sn,'times'=>$time ,'ratio'=>$ratio];
            $num = $num + $ratio;
        }
        if($num == 0){
            return true;
        }
//        var_dump($incr);exit;
        //添加到数据库中,并返回bool值
        return DB::table(env('DB_RATIO'))->insert($incr);

    }

    //分段 数组创建
    public function section($timestamp){
        //如果数组不为空 清空
        if(!empty($this->time_hour)){
            $this->time_hour = [];
        }
        for($i=0;$i<25;$i++){
            $this->time_section[] = ['time'=>$timestamp-(3600*$i),'num'=>0];
        }
    }

    //整点 数组创建
    public function hours($timestamp){
        //如果数组不为空 清空
        if(!empty($this->time_hour)){
            $this->time_hour = [];
        }
        date_default_timezone_set('Asia/Shanghai');
        $time =  date('Y/m/d H:i:s',$timestamp);
        $new_time = substr_replace($time,'00:00',14,5);
        $new_timestamp =  strtotime($new_time);
        for($i=0;$i<24;$i++){
            $this->time_hour[] = ['time'=>$new_timestamp-(3600*$i),'num'=>0];
        }
    }


}
