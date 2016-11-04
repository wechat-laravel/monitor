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

    public function index(){
        return view('backend/ratio');
    }

    public function score($id){
        $post = DB::table(env('DB_SCREEN'))
            ->select('sn','post_at')
            ->where('id', '=', $id)
            ->first();
        if(empty($post)) return $this->bad('该监控信息不存在');
        $this->sn = $post->sn;
        $post_time = $post->post_at;

        $res = DB::table(env('DB_SCREEN_RESULT'))
            ->select('sn', 'read_num', 'like_num', 'updated_at')
            ->where('sn', '=', $this->sn)
            ->orderby('updated_at', 'asc')
            ->get();

        //监控的时候出现错误也会造成没有监控的情况
        if(empty($res)) return $this->bad('该文章的监控数据为空');

        $section = new SectionController();
        foreach ($res as $re => $r){
            if($r->updated_at - $post_time >3600 ) return $this->bad('监控起始时间与文章发布时间相差一个小时以上');
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
        if($arr_count < 11) return $this->bad('监控的有效时间不足12个小时');

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
            return $this->bad('各时间点出现3次以上增量比一致的');
        }
        return $this->mark($incr,$arr_count);



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
    //给每篇文章打分,
    /**
     * @param $sn_arr array 文章各节点的增量比数据
     * @param $count  int 该文章有效的时间段数量
     */
    public function mark($sn_arr,$count){
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
        //总分为文章监控时间段的有效数量*10
        $sum = $count*10;
        $score = intval(($score/$sum)*100);
        $result = ['success' => true, 'value'=>$score];
        return $result;
    }
    //监控有误,不正常的
    public function bad($msg){
        $result = ['success'=> false, 'message'=>$msg];
        return $result;
    }

    public function show(){
        $json = '[
    {
        "value": -1.1618426259,
        "date": "2012-08-28",
        "l": -2.6017329022,
        "u": 0.2949717757
    },
    {
        "value": -0.5828247293,
        "date": "2012-08-29",
        "l": -1.3166963635,
        "u": 0.1324086347
    },
    {
        "value": -0.3790770636,
        "date": "2012-08-30",
        "l": -0.8712221305,
        "u": 0.0956413566
    },
    {
        "value": -0.2792926002,
        "date": "2012-08-31",
        "l": -0.6541832008,
        "u": 0.0717120241
    },
    {
        "value": -0.2461165469,
        "date": "2012-09-01",
        "l": -0.5222677907,
        "u": 0.0594188803
    },
    {
        "value": -0.2017354137,
        "date": "2012-09-02",
        "l": -0.4434280535,
        "u": 0.0419213465
    },
    {
        "value": -0.1457476871,
        "date": "2012-09-03",
        "l": -0.3543957712,
        "u": 0.0623761171
    },
    {
        "value": -0.002610973,
        "date": "2012-09-04",
        "l": -0.3339911495,
        "u": 0.031286929
    },
    {
        "value": -0.0080692734,
        "date": "2012-09-05",
        "l": -0.2951839941,
        "u": 0.0301762553
    },
    {
        "value": -0.0296490933,
        "date": "2012-09-06",
        "l": -0.2964395801,
        "u": -0.0029821004
    },
    {
        "value": 0.001317397,
        "date": "2012-09-07",
        "l": -0.2295443759,
        "u": 0.037903312
    },
    {
        "value": -0.0117649838,
        "date": "2012-09-08",
        "l": -0.2226376418,
        "u": 0.0239720183
    },
    {
        "value": 0.0059394263,
        "date": "2012-09-09",
        "l": -0.2020479849,
        "u": 0.0259489347
    },
    {
        "value": -0.0115565898,
        "date": "2012-09-10",
        "l": -0.2042048037,
        "u": 0.0077863806
    },
    {
        "value": 0.0041183019,
        "date": "2012-09-11",
        "l": -0.1837263172,
        "u": 0.0137898406
    },
    {
        "value": 0.0353559544,
        "date": "2012-09-12",
        "l": -0.136610008,
        "u": 0.051403828
    },
    {
        "value": 0.0070046011,
        "date": "2012-09-13",
        "l": -0.1569988647,
        "u": 0.0202266411
    },
    {
        "value": -0.0004251807,
        "date": "2012-09-14",
        "l": -0.1410340292,
        "u": 0.0273410185
    },
    {
        "value": -0.0035461023,
        "date": "2012-09-15",
        "l": -0.1438653689,
        "u": 0.0165445684
    },
    {
        "value": 0.007797889,
        "date": "2012-09-16",
        "l": -0.1291975355,
        "u": 0.0232461153
    },
    {
        "value": 0.0025402723,
        "date": "2012-09-17",
        "l": -0.133972479,
        "u": 0.0116753921
    },
    {
        "value": -0.005317381,
        "date": "2012-09-18",
        "l": -0.1269266586,
        "u": 0.0129723291
    },
    {
        "value": -0.0075841521,
        "date": "2012-09-19",
        "l": -0.1283478383,
        "u": 0.0056371616
    },
    {
        "value": -0.0391388721,
        "date": "2012-09-20",
        "l": -0.1571172198,
        "u": -0.0311678828
    },
    {
        "value": 0.0075430252,
        "date": "2012-09-21",
        "l": -0.1097354417,
        "u": 0.0141132062
    },
    {
        "value": 0.1850284663,
        "date": "2012-09-22",
        "l": 0.0333682152,
        "u": 0.2140709422
    },
    {
        "value": 0.076629596,
        "date": "2012-09-23",
        "l": -0.0068472967,
        "u": 0.1101280569
    },
    {
        "value": -0.0314292271,
        "date": "2012-09-24",
        "l": -0.1074281762,
        "u": 0.0032669363
    },
    {
        "value": -0.0232608674,
        "date": "2012-09-25",
        "l": -0.0905197842,
        "u": 0.0164250295
    },
    {
        "value": -0.01968615,
        "date": "2012-09-26",
        "l": -0.084319856,
        "u": 0.0193319465
    },
    {
        "value": -0.0310196816,
        "date": "2012-09-27",
        "l": -0.0914356781,
        "u": 0.0094436256
    },
    {
        "value": -0.0758746967,
        "date": "2012-09-28",
        "l": -0.1169814745,
        "u": -0.019659551
    },
    {
        "value": 0.0233974572,
        "date": "2012-09-29",
        "l": -0.0356839258,
        "u": 0.0610712506
    },
    {
        "value": 0.011073579,
        "date": "2012-09-30",
        "l": -0.0558712863,
        "u": 0.0346160081
    },
    {
        "value": -0.002094822,
        "date": "2012-10-01",
        "l": -0.0707143388,
        "u": 0.0152899266
    },
    {
        "value": -0.1083707096,
        "date": "2012-10-02",
        "l": -0.1718101335,
        "u": -0.0886271057
    },
    {
        "value": -0.1098258972,
        "date": "2012-10-03",
        "l": -0.1881274065,
        "u": -0.1072157972
    },
    {
        "value": -0.0872970297,
        "date": "2012-10-04",
        "l": -0.1731903321,
        "u": -0.064381434
    },
    {
        "value": -0.0761992047,
        "date": "2012-10-05",
        "l": -0.1770373817,
        "u": 0.100085727
    },
    {
        "value": -0.0416654249,
        "date": "2012-10-06",
        "l": -0.1502479611,
        "u": 0.0751148102
    },
    {
        "value": -0.0410128962,
        "date": "2012-10-07",
        "l": -0.1618694445,
        "u": 0.0881453482
    },
    {
        "value": -0.0214289042,
        "date": "2012-10-08",
        "l": -0.1590852977,
        "u": 0.0871880288
    },
    {
        "value": 0.2430880604,
        "date": "2012-10-09",
        "l": 0.063624221,
        "u": 0.2455101587
    },
    {
        "value": 0.3472823479,
        "date": "2012-10-10",
        "l": 0.1553854927,
        "u": 0.3583991097
    },
    {
        "value": 0.3360734074,
        "date": "2012-10-11",
        "l": 0.2055952772,
        "u": 0.3812162823
    },
    {
        "value": -0.0463648355,
        "date": "2012-10-12",
        "l": -0.0626466998,
        "u": 0.0037342957
    },
    {
        "value": -0.0867009379,
        "date": "2012-10-13",
        "l": -0.0867594055,
        "u": -0.0223791074
    },
    {
        "value": -0.1288672826,
        "date": "2012-10-14",
        "l": -0.1161709129,
        "u": -0.0534789124
    },
    {
        "value": -0.1474426821,
        "date": "2012-10-15",
        "l": -0.1559759048,
        "u": -0.0646995092
    },
    {
        "value": -0.1502405066,
        "date": "2012-10-16",
        "l": -0.1604364638,
        "u": -0.0602562376
    },
    {
        "value": -0.1203765529,
        "date": "2012-10-17",
        "l": -0.1569023195,
        "u": -0.0578129637
    },
    {
        "value": -0.0649122919,
        "date": "2012-10-18",
        "l": -0.0782987564,
        "u": -0.0501999174
    },
    {
        "value": -0.015525562,
        "date": "2012-10-19",
        "l": -0.1103873808,
        "u": -0.0132131311
    },
    {
        "value": -0.006051357,
        "date": "2012-10-20",
        "l": -0.1089644497,
        "u": 0.0230384197
    },
    {
        "value": 0.0003154213,
        "date": "2012-10-21",
        "l": -0.1073849227,
        "u": 0.0017290437
    },
    {
        "value": -0.0063018298,
        "date": "2012-10-22",
        "l": -0.1120298155,
        "u": 0.0173284555
    },
    {
        "value": -0.004294834,
        "date": "2012-10-23",
        "l": -0.1076841119,
        "u": 0.0547933965
    },
    {
        "value": -0.0053400832,
        "date": "2012-10-24",
        "l": -0.1096991408,
        "u": 0.0560555803
    },
    {
        "value": 0.0070057212,
        "date": "2012-10-25",
        "l": -0.0940613813,
        "u": 0.0425517607
    },
    {
        "value": 0.0082121656,
        "date": "2012-10-26",
        "l": -0.0906810455,
        "u": 0.0396884383
    },
    {
        "value": 0.0141422884,
        "date": "2012-10-27",
        "l": -0.0841305678,
        "u": 0.0340050012
    },
    {
        "value": 0.0041613553,
        "date": "2012-10-28",
        "l": -0.0886723749,
        "u": 0.039426727
    },
    {
        "value": -0.0013614287,
        "date": "2012-10-29",
        "l": -0.0923481608,
        "u": 0.0438725574
    },
    {
        "value": -0.0052144933,
        "date": "2012-10-30",
        "l": -0.0937763043,
        "u": 0.0459998555
    },
    {
        "value": 0.0078904741,
        "date": "2012-10-31",
        "l": -0.0807028001,
        "u": 0.0334824169
    },
    {
        "value": 0.0099598702,
        "date": "2012-11-01",
        "l": -0.0740001323,
        "u": 0.0280264274
    },
    {
        "value": 0.0001146029,
        "date": "2012-11-02",
        "l": -0.0820430294,
        "u": 0.0326771125
    },
    {
        "value": 0.0047572651,
        "date": "2012-11-03",
        "l": -0.0754113825,
        "u": 0.0294912577
    },
    {
        "value": 0.006204557,
        "date": "2012-11-04",
        "l": -0.0750627059,
        "u": 0.029693607
    },
    {
        "value": 0.0115231406,
        "date": "2012-11-05",
        "l": -0.0663484142,
        "u": 0.0214084056
    },
    {
        "value": -0.0032634994,
        "date": "2012-11-06",
        "l": -0.0793170451,
        "u": 0.0355159827
    },
    {
        "value": -0.0108985452,
        "date": "2012-11-07",
        "l": -0.0846123893,
        "u": 0.0409797057
    },
    {
        "value": -0.0092766813,
        "date": "2012-11-08",
        "l": -0.0802668328,
        "u": 0.0373886301
    },
    {
        "value": 0.0095972086,
        "date": "2012-11-09",
        "l": -0.0623739694,
        "u": 0.0194918693
    },
    {
        "value": -0.0111809358,
        "date": "2012-11-10",
        "l": -0.0819555908,
        "u": 0.038335749
    },
    {
        "value": -0.0023572296,
        "date": "2012-11-11",
        "l": -0.0745443377,
        "u": 0.0306093592
    },
    {
        "value": 0.0084213775,
        "date": "2012-11-12",
        "l": -0.0657707155,
        "u": 0.0227270619
    },
    {
        "value": 0.0107446453,
        "date": "2012-11-13",
        "l": -0.0617995017,
        "u": 0.0196547867
    },
    {
        "value": 0.009457792,
        "date": "2012-11-14",
        "l": -0.0597697849,
        "u": 0.0191832343
    },
    {
        "value": 0.0031194779,
        "date": "2012-11-15",
        "l": -0.0589126783,
        "u": 0.0186409442
    },
    {
        "value": -0.0115128213,
        "date": "2012-11-16",
        "l": -0.0767105447,
        "u": 0.0370292452
    },
    {
        "value": 0.0058347339,
        "date": "2012-11-17",
        "l": -0.0592236472,
        "u": 0.0198181452
    },
    {
        "value": -0.0235630436,
        "date": "2012-11-18",
        "l": -0.083529944,
        "u": 0.046280909
    },
    {
        "value": -0.0479795964,
        "date": "2012-11-19",
        "l": -0.1086422529,
        "u": 0.0113044645
    },
    {
        "value": -0.0218184359,
        "date": "2012-11-21",
        "l": -0.0881634878,
        "u": 0.0448568265
    },
    {
        "value": -0.0071361172,
        "date": "2012-11-28",
        "l": -0.0807350229,
        "u": 0.0453599734
    },
    {
        "value": -0.0151966912,
        "date": "2012-12-05",
        "l": -0.089995793,
        "u": 0.0558329569
    },
    {
        "value": -0.0097784855,
        "date": "2012-12-12",
        "l": -0.089466481,
        "u": 0.0550191387
    },
    {
        "value": -0.0095681495,
        "date": "2012-12-19",
        "l": -0.090513354,
        "u": 0.057073314
    },
    {
        "value": -0.0034165915,
        "date": "2012-12-27",
        "l": -0.0907151292,
        "u": 0.0561479112
    },
    {
        "value": 0.3297981389,
        "date": "2012-12-31",
        "l": 0.1537781522,
        "u": 0.3499473316
    }
]';
        return $json;

    }

    public function destroy($id)
    {
        //
    }
}
