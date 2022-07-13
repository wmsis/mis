<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Models\SIS\AlarmRecord;
use App\Http\Models\SIS\HistorianTag;
use HistorianService;
use Illuminate\Support\Facades\Cache;
use App\Events\AlarmNotifyEvent;
use JPushService;
use Illuminate\Support\Facades\DB;
use WechatService;
use Log;

class RecordAlarm extends Command
{
    const AJAX_SUCCESS = 0;
    const AJAX_FAIL = -1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alarm:record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'alarm record';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * 一分钟内运行1次，计划任务每分钟运行一次
     */
    public function handle()
    {
        //从配置信息读取tagslist的配置值
        DB::table('historian_tag')->select('*')
            ->orderBy('id', 'asc')
            ->chunk(100, function($tagslist) {
                if ($tagslist && !empty($tagslist)) {
                    $settingTags = array();
                    $tagNames = '';
                    foreach ($tagslist as $item) {
                        //是否设置了报警阀值
                        if ($item->upper_limit && $item->lower_limit) {
                            $settingTags[] = $item;
                            if ($tagNames) {
                                $tagNames = $tagNames . ';' . $item->tag_name;
                            } else {
                                $tagNames = $item->tag_name;
                            }
                        }
                    }

                    if (count($settingTags) > 0) {
                        //从historian读取该tag当前值
                        $currentData = HistorianService::currentData($tagNames);
                        if ($currentData['code'] == self::AJAX_SUCCESS && $currentData['data']['ErrorCode'] == 0) {
                            $datalist = $currentData['data']['Data'];
                            foreach ($settingTags as $item) {
                                $key = md5($item->id);
                                foreach ($datalist as $data) {
                                    if ($data['ErrorCode'] == 0 && $item->tag_name == $data['TagName']) {
                                        $val = $data['Samples'][0];
                                        //判断是否报警
                                        if ($item->upper_limit < $val['Value'] || $item->lower_limit > $val['Value']) {
                                            //超过报警界限，触发报警
                                            if (Cache::has($key)) {
                                                //已经报过警，5分钟以内没有报过警就报警，否则不通知
                                                $this->notify($item->id);
                                            } else {
                                                //没有报过警，记录缓存
                                                $v = true;
                                                Cache::add($key, $v, 3600); //缓存保存一个小时

                                                //插入报警记录，报警开始时间，tag_id，当前报警参考上限值和下限值
                                                $record = AlarmRecord::create([
                                                    "tag_id" => $item->id,
                                                    "start_time" => date('Y-m-d H:i:s', strtotime($val['TimeStamp'])),
                                                    "upper_limit" => $item->upper_limit,
                                                    "lower_limit" => $item->lower_limit
                                                ]);

                                                if($record){
                                                    $this->notify($item->id);
                                                }
                                            }
                                        } else {
                                            if (Cache::has($key)) {
                                                //已经报过警，清除缓存
                                                Cache::forget($key);
                                                //记录报警结束时间
                                                $row = AlarmRecord::where("tag_id", $item->id)->orderBy('id', 'desc')->first();
                                                $row->end_time = date('Y-m-d H:i:s', strtotime($val['TimeStamp']));
                                                $row->save();
                                            }
                                        }
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                else{
                    return false;
                }
            });
    }

    private function notify($tag_id){
        $record = AlarmRecord::where('tag_id', $tag_id)->orderBy('id', 'DESC')->first();
        $tagObj = HistorianTag::find($tag_id);
        $users = $tagObj->users;
        if ($users && $record) {
            foreach ($users as $key => $user) {
                $period = $user->period;
                $start = '';
                $end = '';
                if($period){
                    $date = date('Y-m-d');
                    $class_arr = $this->computerClass($date);
                    foreach ($class_arr as $class){
                        if($period == $class['en_name']){
                            $start = $class['start'];
                            $end = $class['end'];
                        }
                    }
                }

                //是否在上班时间内
                if((!$start && !$end) || ($start && $end && strtotime($start) < time() && time() < strtotime($end))) {
                    $kk = md5($tag_id.'has_notify');
                    if (!Cache::has($kk)) {
                        //5分钟以内没有报过警就报警，否则不通知

                        //AlarmNotifyEvent
                        event(new AlarmNotifyEvent($user, $record));
                        //jpush
                        //$this->jpush($user, $tagObj);
                        //$this->wxmsg($user, $record, $tagObj);

                        //第一次报警，保存缓存
                        $v = true;
                        Cache::add($kk, $v, 5 * 60);
                    }
                }
            }
        }
    }

    private function jpush($user, $item){
        JPushService::send([
            "notification" => array(
                "alert" => "您有新的TAG报警"
            ),
            "androidNotification" => array(
                "alert" => "您有新的TAG报警",
                "options" => array(
                    'title' => 'TAG报警了',
                    'extras' => array(
                        'tag_id'=> $item->id
                    )
                )
            ),
            "message" => array(
                "message_content" => "TAG".$item->tag_name."报警了",
                "options" => array(
                    'title' => 'TAG报警了',
                    'extras' => array(
                        'tag_id'=> $item->id
                    )
                )
            )
        ]);
    }

    private function wxmsg($user, $record, $tag){
        $member = $user->member;
        if($member) {
            $wechats = $member->wechats;
            if($wechats){
                foreach ($wechats as $item){
                    $appid = config('wechat.appid');
                    $appsecret = config('wechat.appsecret');
                    $platform = config('wechat.platform');
                    if($platform == $item->platform) {
                        $openid = $item->openid;
                        $postData = array(
                            "touser" => $openid,
                            "template_id" => config('template.tag_alarm'),
                            "url" => "",
                            "data" => array(
                                "first" => array(
                                    "value" => "您有新的报警需要处理",
                                    "color" => "#173177"
                                ),
                                "keyword1" => array(
                                    "value" => date('Y-m-d H:i:s'),
                                    "color" => "#173177"
                                ),
                                "keyword2" => array(
                                    "value" => $tag->tag_name,
                                    "color" => "#173177"
                                ),
                                "remark" => array(
                                    "value" => "快处理报警吧",
                                    "color" => "#173177"
                                )
                            )
                        );
                        WechatService::sendTemplateMessage($appid, $appsecret, $postData);
                        break;
                    }
                }
            }
        }
    }

    private function computerClass($date){
        $timeArr = array();
        $classDemo = config('classschedule.classDemo');
        $classTime = config('classschedule.classTime');
        $demoClassTime = array_keys($classDemo);
        $len = count($demoClassTime);

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
            foreach ($classTime as $itemFirst){
                foreach ($classDemo[$dkey] as $itemSecond) {
                    if($itemFirst['en_name'] == $itemSecond['en_name']) {
                        $endDate = $date;
                        if($itemFirst['en_name'] == 'third'){
                            //如果是晚班，则跨天，结束日期为
                            $endDate = date('Y-m-d', strtotime($date) + 24 * 60 * 60);
                        }

                        $timeArr[] = array(
                            "cn_name" => $itemFirst['cn_name'],
                            "en_name" => $itemFirst['en_name'],
                            "class_name" => $itemSecond['class_name'], //甲乙丙丁
                            "start" => $date . " " . $itemFirst['start'],
                            "end" => $endDate . " " . $itemFirst['end']
                        );
                        break;
                    }
                }
            }
        }

        return $timeArr;
    }
}
