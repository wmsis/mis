<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\SIS\DcsStandard;
use App\Models\Mongo\HistorianFormatData;
use App\Models\MIS\AlarmRule;
use App\Models\MIS\Alarm;
use App\Models\MIS\AlarmRecord;
use App\Events\AlarmEvent;
use Log;

class AlarmDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $factory;
    protected $tenement_conn; //租户连接
    protected $tenement_mongo_conn; //租户连接
    protected $historian_day_data_table; //本地日累计数据表
    protected $historian_format_data_table; //本地保存的格式化后的数据集合
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params=null)
    {
        $this->factory = $params && isset($params['factory']) ? $params['factory'] : '';
        $this->tenement_conn = $params && isset($params['tenement_conn']) ? $params['tenement_conn'] : '';
        $this->tenement_mongo_conn = $params && isset($params['tenement_mongo_conn']) ? $params['tenement_mongo_conn'] : '';
        $this->historian_day_data_table = $params && isset($params['historian_day_data_table']) ? $params['historian_day_data_table'] : '';
        $this->historian_format_data_table = $params && isset($params['historian_format_data_table']) ? $params['historian_format_data_table'] : '';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('0000000000000000');
        $alarm_obj = (new Alarm())->setConnection($this->tenement_conn); //连接特定租户下面的报警数据表
        $alarm_record_obj = (new AlarmRecord())->setConnection($this->tenement_conn);
        $historian_format_data_obj = (new HistorianFormatData())->setConnection($this->tenement_mongo_conn)->setTable($this->historian_format_data_table);//连接特定租户下面的格式化后的历史数据表
        $lists = DB::connection($this->tenement_conn)->table('dcs_standard')
            ->join('alarm_rule', 'alarm_rule.dcs_standard_id', '=', 'dcs_standard.id')
            ->select('alarm_rule.*', 'dcs_standard.cn_name', 'dcs_standard.en_name')
            ->get();

        foreach ($lists as $key => $item) {
            Log::info('11111111111111111111');
            //最新的DCS值
            $latest_historian_data = $historian_format_data_obj->where('dcs_standard_id', $item->dcs_standard_id)
                ->orderBy('datetime', 'desc')
                ->first();

            //获取最新报警记录
            $latest_alarm_record = $alarm_record_obj->where('alarm_rule_id', $item->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if($latest_historian_data && $latest_historian_data->value){
                Log::info('22222222222222222222222');
                Log::info($latest_historian_data->value);
                Log::info($item->max_value);
                Log::info($item->min_value);
                //判断是否报警
                if($latest_historian_data->value >= $item->max_value || $latest_historian_data->value <= $item->min_value){
                    Log::info('33333333333333333');
                    if(($latest_alarm_record && $latest_alarm_record->end_time) || !$latest_alarm_record){
                        Log::info('55555555555555555555');
                        //有报警记录，并且报警结束时间不为空,或，或者没有报警记录，创建报警记录，记录报警开始时间
                        $alarm_record_obj->create([
                            "alarm_rule_id" => $item->id,
                            "max_value" => $item->max_value,
                            "min_value" => $item->min_value,
                            "start_time" => date('Y-m-d H:i:s'),
                            "orgnization_id" => $this->factory->id
                        ]);
                    }
                }
                else{
                    Log::info('666666666666666666666');
                    //有报警并且没有记录报警结束时间，记录报警结束时间
                    if($latest_alarm_record && !$latest_alarm_record->end_time){
                        Log::info('7777777777777777');
                        $latest_alarm_record->end_time = date('Y-m-d H:i:s');
                        $latest_alarm_record->save();

                        $time_diff = time() - (int)strtotime($latest_alarm_record->start_time);
                        $set_alarm_sustain = $item->period * $item->sustain;
                        //报警时长大于设置的时长，需要报警，否则不需要
                        if($time_diff > $set_alarm_sustain){
                            Log::info('88888888888888888888888');
                            $content = '报警上限值为' . $item->max_value . '，下限值为' . $item->min_value . '，已经持续报警了' . $time_diff . '秒，当前值为' . $latest_historian_data->value;
                            $alarm_obj->create([
                                "alarm_rule_id" => $item->id,
                                "status" => 'init',
                                "content" => $content,
                                "orgnization_id" => $this->factory->id
                            ]);

                            //事件发生调度
                            AlarmEvent::dispatch($alarm_obj, $this->tenement_conn);
                        }
                    }
                }
            }
        }
    }
}
