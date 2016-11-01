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
    public  $time_hour    = [];

    //文章的唯一标识
    protected $sn ;

    public function index()
    {
        $id  = $this->getId();
        $now = time();
        $res = DB::table(env('DB_SCREEN'))
            ->select('wechat_id','sn','post_at','begin_at')
            ->where('end_at','<',$now)
            ->where('days','=',1)
            ->where('id','>',$id)
            ->offset(0)
            ->limit(500)
//            ->where('sn','=','77c9cd42c144e23a37ce0a1dd5bae33d')
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
            $post_time = $ob->post_at;

            //过滤掉监控时间与文章发布时间相差一个小时的

            //$res一个sn的所有记录
            $res = DB::table(env('DB_SCREEN_RESULT'))
                ->select('sn', 'read_num', 'like_num', 'updated_at')
                ->where('sn', '=', $ob->sn)
                ->orderby('updated_at', 'asc')
                ->get();

            //监控的时候出现错误也会造成没有监控的情况
            if(empty($res)) continue;
            $num = end($res)->read_num;
            //阅读量的范围
            if ($num < 8000 || $num > 100000) continue;

            foreach ($res as $re) {
                //updated_at是按照正叙来排的  如果记录开始监控的时间与真实开始监控的时间相差一个小时 跳过
                if ($re->updated_at - $post_time > 3600) break;

                //判断该sn是否已经存在表中,存在跳过
                $exists = DB::table(env('DB_RATIO'))
                    ->select('sn')
                    ->where('sn','=',$re->sn)
                    ->first();
                if(!empty($exists)) break;

                $this->sn = $re->sn;
                $this->hours($re->updated_at);
                $this->monitorHours($res);
                $result = $this->hoursIncr();
                if (!$result) {
                    exit($re->sn . '有误');
                } else {
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
                    //监控的时间戳是正叙的,刚比这个时间段大的,表示这个时刻最大的值
                    if($ob->updated_at >= $v['time']){
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
        //因为有可能会出现,监控的文章不够24个小时的这种情况出现.致使末尾最终阅读量为0
        //先倒叙,然后连续的零有几个,直到遇见的不是零为止
        $arr = array_reverse($this->time_hour);
        $arr_num = 0;
        foreach ($arr as $k => $v){
            if($v['num'] == 0){
                $arr_num += 1;
            }else{
                //如果遇到不为零的直接跳出
                break;
            }
        }
        for ($i=0; $i<$arr_num; $i++){
            array_pop($this->time_hour);
        }
        $arr_count = count($this->time_hour)-1;
        //增量
        $sum = $this->time_hour[$arr_count]['num'] - $this->time_hour[0]['num'];
        //计算比值总和,如果等于零,表示该监控记录有误
        $num = 0;
        $incr = [];
        //记录ratio相等的个数
        $same = [];
        for ($i = 0;$i<$arr_count;$i++){
            $time = date('Y/m/d H:i:s',$this->time_hour[$i+1]['time']);
            $time = intval(substr($time,11,2));
            //ratio 比值 向上保留万分数 除以100
            if($this->time_hour[$i+1]['num'] - $this->time_hour[$i]['num'] != 0){
                $ratio=intval((ceil((($this->time_hour[$i+1]['num'] - $this->time_hour[$i]['num'])/$sum)*10000)));
            }else{
                $ratio=0;
            }

            $same [] = $ratio;

            $incr[] = ['sn'=>$this->sn,'times'=>$time ,'ratio'=>$ratio];

            $num = $num + $ratio;
        }
        //统计ratio出现一样的次数
        $same_arr  = array_count_values($same);
        //计算有几个单元
        $same_time = count($same_arr);
        //计算次数的总和,正常的`和`应该等于单元数,每个单元出现的次数为1,如果总和比单元多两点,
        //表示ratio出现3词以上一致 或者有多个ratio出现一致,则过滤掉
        $same_sum  = array_sum($same_arr);

        if($same_sum - $same_time > 2){
            return true;
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
        //先清空该数组
        $this->time_hour = [];

        date_default_timezone_set('Asia/Shanghai');
        $time =  date('Y/m/d H:i:s',$timestamp);
        //如果等于23的话,+1 要取00点 并且前面日期也要+1
        $new_time = substr_replace($time,'00:00',14,5);
        $new_timestamp =  strtotime($new_time)+3600;
        for($i=0;$i<24;$i++){
            $this->time_hour[] = ['time'=>$new_timestamp+(3600*$i),'num'=>0];
        }
    }

    //查出不正常的sn
    public function abnormal(){
        $res = DB::table(env('DB_RATIO'))
            ->select('sn')
            ->where('ratio','=',0)
            ->where('times','>=',0)
            ->where('times','<=',2)
            ->groupBy('sn')
//            ->havingRaw(' count(sn) < 12 ')
            ->get();
        foreach ($res as $re){
             $result = $this->delete($re->sn);
             if(!$result) exit($re->sn);
        }
        return 'ok';
    }
    //删除不正常的sn
    public function delete($sn){
        $res = DB::table(env('DB_RATIO'))
            ->where('sn','=',$sn)
            ->delete();
        return $res;
    }


}
