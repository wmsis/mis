<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Models\SIS\CheckAlarmRecord;
use App\Http\Models\SIS\DiyClass;
use App\Http\Models\SIS\ClassCheckStatistics;
use App\Http\Models\SIS\BoilerCheck;
use Log;

class CheckStatistics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $date;
    public $tries = 3;

    /**
     * 统计当天的锅炉考核指标数据
     *
     * @return void
     */
    public function __construct($date=null)
    {
        $this->date = $date ? $date : date('Y-m-d', time());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //1、获取当天排班情况，有无自定义排班
        $class_flag = false; //是否有设置自定义排班
        $date = $this->date;
        $dateArr = array($date);
        $timeArr = $this->computerClass($dateArr);
        $defaultDateClass = array(
            'date' => substr($timeArr[0]['start'], 0, 10),
            'morning_value' => $timeArr[0]['class_name'],
            'morning_begin' => $timeArr[0]['start'],
            'morning_end' => $timeArr[0]['end'],
            'day_value' => $timeArr[1]['class_name'],
            'day_begin' => $timeArr[1]['start'],
            'day_end' => $timeArr[1]['end'],
            'evening_value' => $timeArr[2]['class_name'],
            'evening_begin' => $timeArr[2]['start'],
            'evening_end' => $timeArr[2]['end'],
        );

        $dateClass = DiyClass::where('date', $date)->get(); //此时为多维数组
        if(!$dateClass || count($dateClass) == 0){
            $class_flag = false;
        }
        else{
            $class_flag = true;
        }

        //所有指标
        $tags = BoilerCheck::all();
        //统计每个指标在早白中三班中的报警值
        foreach ($tags as $key => $item) {
            //2、计算各个班所在时间内的报警统计值
            $morning_num = 0;
            $day_num = 0;
            $evening_num = 0;
            $morning_begin = null;
            $morning_end = null;
            $morning_value = null;
            $day_begin = null;
            $day_end = null;
            $day_value = null;
            $evening_begin = null;
            $evening_end = null;
            $evening_value = null;
            $match_flag = false;  //是否有匹配的自定义排班
            if($class_flag){
                foreach ($dateClass as $k => $v) {
                    if($v->module_id == $item->module_id){
                        //某个锅炉有自己的排班时间
                        $morning_begin = $v['morning_begin'];
                        $morning_end = $v['morning_end'];
                        $morning_value = $v['morning_value'];
                        $day_begin = $v['day_begin'];
                        $day_end = $v['day_end'];
                        $day_value = $v['day_value'];
                        $evening_begin = $v['evening_begin'];
                        $evening_end = $v['evening_end'];
                        $evening_value = $v['evening_value'];
                        $match_flag = true;
                        break;
                    }
                    else{
                        continue;
                    }
                }
            }

            if(!$match_flag){
                //此时所有锅炉都按默认排班时间
                $morning_begin = $defaultDateClass['morning_begin'];
                $morning_end = $defaultDateClass['morning_end'];
                $morning_value = $defaultDateClass['morning_value'];
                $day_begin = $defaultDateClass['day_begin'];
                $day_end = $defaultDateClass['day_end'];
                $day_value = $defaultDateClass['day_value'];
                $evening_begin = $defaultDateClass['evening_begin'];
                $evening_end = $defaultDateClass['evening_end'];
                $evening_value = $defaultDateClass['evening_value'];
            }

            //此时所有锅炉都按默认排班时间
            if($morning_begin){
                $morning_num = CheckAlarmRecord::where('boiler_check_id', $item->id)
                    ->where('start_time', '>=', $morning_begin)
                    ->where('need_notify', 1)
                    ->count();
            }

            if($day_begin){
                $day_num = CheckAlarmRecord::where('boiler_check_id', $item->id)
                    ->where('start_time', '>=', $day_begin)
                    ->where('need_notify', 1)
                    ->count();
            }

            if($evening_begin){
                $evening_num = CheckAlarmRecord::where('boiler_check_id', $item->id)
                    ->where('start_time', '>=', $evening_begin)
                    ->where('need_notify', 1)
                    ->count();
            }

            //3、插入各班的统计值
            ClassCheckStatistics::updateOrCreate(
                [
                    'module_id' => $item['module_id'],
                    'class_date' => $date,
                    'class_name' => $date . '日早班'. $morning_value .'值',
                    'tag_en_name' => $item['tag_en_name']
                ],
                [
                    'tag_cn_name' => $item['tag_cn_name'],
                    'start_time' => $morning_begin,
                    'end_time' => $morning_end,
                    'duty_name' => $morning_value,
                    'value' => $morning_num
                ]
            );

            ClassCheckStatistics::updateOrCreate(
                [
                    'module_id' => $item['module_id'],
                    'class_date' => $date,
                    'class_name' => $date . '日白班'. $day_value .'值',
                    'tag_en_name' => $item['tag_en_name']
                ],
                [
                    'tag_cn_name' => $item['tag_cn_name'],
                    'start_time' => $day_begin,
                    'end_time' => $day_end,
                    'duty_name' => $day_value,
                    'value' => $day_num
                ]
            );

            ClassCheckStatistics::updateOrCreate(
                [
                    'module_id' => $item['module_id'],
                    'class_date' => $date,
                    'class_name' => $date . '日中班'. $evening_value .'值',
                    'tag_en_name' => $item['tag_en_name']
                ],
                [
                    'tag_cn_name' => $item['tag_cn_name'],
                    'start_time' => $evening_begin,
                    'end_time' => $evening_end,
                    'duty_name' => $evening_value,
                    'value' => $evening_num
                ]
            );
        }
    }

    private function computerClass($dateArr){
        $timeArr = array();
        $classDemo = config('classschedule.classDemo');
        $classTime = config('classschedule.classTime');
        $demoClassTime = array_keys($classDemo);
        $len = count($demoClassTime);
        foreach ($dateArr as $date){
            $time1 = strtotime($demoClassTime[0]);
            $time2 = strtotime($date);
            $dkey = '';

            if($time2 >= $time1){
                $diffSeconds = $time2 - $time1;
                $diffDays = $diffSeconds/(24*60*60);
                $div = $diffDays%$len;
                switch($div){
                    case 0:
                        $dkey = $demoClassTime[0];
                        break;
                    case 1:
                        $dkey = $demoClassTime[1];
                        break;
                    case 2:
                        $dkey = $demoClassTime[2];
                        break;
                    case 3:
                        $dkey = $demoClassTime[3];
                        break;
                    case 4:
                        $dkey = $demoClassTime[4];
                        break;
                    case 5:
                        $dkey = $demoClassTime[5];
                        break;
                    case 6:
                        $dkey = $demoClassTime[6];
                        break;
                    case 7:
                        $dkey = $demoClassTime[7];
                        break;
                    default:
                        break;
                }
            }
            else{
                $diffSeconds = $time1 - $time2;
                $diffDays = $diffSeconds/(24*60*60);
                $div = $diffDays%$len;
                switch($div){
                    case 1:
                        $dkey = $demoClassTime[$len - 1];
                        break;
                    case 2:
                        $dkey = $demoClassTime[$len - 2];
                        break;
                    case 3:
                        $dkey = $demoClassTime[$len - 3];
                        break;
                    case 4:
                        $dkey = $demoClassTime[$len - 4];
                        break;
                    case 5:
                        $dkey = $demoClassTime[$len - 5];
                        break;
                    case 6:
                        $dkey = $demoClassTime[$len - 6];
                        break;
                    case 7:
                        $dkey = $demoClassTime[$len - 7];
                        break;
                    default:
                        break;
                }
            }

            if($dkey) {
                $this->mergeClass($classTime, $classDemo[$dkey], $date, $timeArr);
            }
        }

        return $timeArr;
    }

    private function mergeClass($arr1, $arr2, $date, &$timeArr){
        foreach ($arr1 as $itemFirst){
            foreach ($arr2 as $itemSecond) {
                if($itemFirst['en_name'] == $itemSecond['en_name']) {
                    $endDate = $date;
                    if($itemFirst['en_name'] == 'third'){
                        //如果是晚班，则跨天，结束日期为
                        $endDate = date('Y-m-d', strtotime($date) + 24 * 60 * 60);
                    }
                    $timeArr[] = array(
                        "cn_name" => $itemFirst['cn_name'],
                        "en_name" => $itemFirst['en_name'],
                        "class_name" => $itemSecond['class_name'],
                        "start" => $date . " " . $itemFirst['start'],
                        "end" => $endDate . " " . $itemFirst['end']
                    );
                    break;
                }
            }
        }
    }
}
