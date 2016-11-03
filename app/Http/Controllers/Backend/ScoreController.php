<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Backend\SectionController;
use App\Http\Requests;
use App\Http\Controllers\Controller;

//得出结果
class ScoreController extends Controller
{
    public $sn = null;

    public function score($id){
        $post = DB::table(env('DB_SCREEN'))
            ->select('sn','post_at')
            ->where('id', '=', $id)
            ->first();
        $this->sn = $post->sn;
        $post_time = $post->post_at;

        $res = DB::table(env('DB_SCREEN_RESULT'))
            ->select('sn', 'read_num', 'like_num', 'updated_at')
            ->where('sn', '=', $this->sn)
            ->orderby('updated_at', 'asc')
            ->get();

        //监控的时候出现错误也会造成没有监控的情况
        if(empty($res)) return $this->bad($this->sn,'该文章的监控数据为空');

        $section = new SectionController();
        foreach ($res as $re => $r){
            if($r->updated_at - $post_time >3600 ) return $this->bad($this->sn,'开始监控时间与文章发布时间相差太多');
            $section->hours($r->updated_at);
            $section->monitorHours($res);
            break;
        }
        $time_hour = $section->time_hour;
        return $this->screen($time_hour);
    }

    public function screen($time){
        $arr = array_reverse($time);
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
            array_pop($time);
        }
        $arr_count = count($time)-1;
        //因为这里减一了,如果<11 说明不足12个小时,数据就没有什么意义了
        if($arr_count < 11) return $this->bad($this->sn,'监控的有效时间不足12个小时');

        //增量
        $sum = $time[$arr_count]['num'] - $time[0]['num'];
        //计算比值总和,如果等于零,表示该监控记录有误
        $num = 0;
        $incr = [];

        //记录ratio相等的个数
        $same = [];
        for ($i = 0;$i<$arr_count;$i++){
            $times = date('Y/m/d H:i:s',$time[$i+1]['time']);
            $times = intval(substr($times,11,2));
            //ratio 比值 向上保留万分数 除以100
            if($time[$i+1]['num'] - $time[$i]['num'] != 0){
                $ratio=intval((ceil((($time[$i+1]['num'] - $time[$i]['num'])/$sum)*10000)));
            }else{
                $ratio=0;
            }

            $same [] = $ratio;

            $incr[] = ['sn'=>$this->sn,'times'=>$times ,'ratio'=>$ratio];

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
            return $this->bad($this->sn,'各时间点出现3次以上增量比一致的');
        }
        return $this->mark($incr);




    }
    
    //统计每个时间段发文的每个点的平均增长比值
    public function avg($time){

        $times = intval($time);

        $avg_arr = [];
        for($i=0;$i<23;$i++){
            if($times == 24){
                $times = 0;
            }
            $avg_arr[] = DB::select('SELECT avg(ratio) as avg_ratio FROM '.env('DB_RATIO').' WHERE sn IN (SELECT sn FROM '.env('DB_RATIO_STAR')." WHERE times={$time}) AND times={$times}");

            $times +=1;
        }
        return $avg_arr;
    }
    //给每篇文章打分
    public function mark($sn_arr){
        foreach ($sn_arr as $re => $r){
            //循环取一次,第一个为开始的时间
            $ratios = $this->avg($r['times']);
            break;
        }
//        var_dump($ratios[0][0]->avg_ratio);exit;
        $i= 0 ;
        $score= 0;
        foreach ($sn_arr as $re => $r){
            //当前ratio的平均值
            $avg_ratio = intval($ratios[$i][0]->avg_ratio);
            //分10份,以此时$avg_ratio的值为中心,如果sn的ratio在基数加减5份之内,为满分,超过一份-2分
            $length_ratio = $avg_ratio/10;
            //算出范围
            $range= ceil((abs($r['ratio'] - $avg_ratio))/$length_ratio);
            //开始打分
            if($range > 5){
                //每超过一份扣2分
                $score += 10 - $range*2;
            }else{
                $score += 10;
            }
            $i ++;
        }
        //总分为230分
        $score = intval(($score/230)*100);
        var_dump($score);
    }
    //监控有误,不正常的
    public function bad($sn,$msg){
        $result = $msg;
        return $result;
    }

    public function destroy($id)
    {
        //
    }
}
