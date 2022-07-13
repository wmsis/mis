<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Models\SIS\DailyData;
use App\Http\Models\SIS\EconomyRunData;
use App\Http\Models\SIS\EconomyDaily;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Faker\Factory;
use EconomyDailyService;

class EconomyDailyExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $date;
    public $tries = 3;

    /**
     * 导出经济日报表
     *
     * @return void
     */
    public function __construct($date=null)
    {
        $this->date = $date ? $date : date('Y-m-d');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', -1);
        $data = $this->statementData();

        // download 方法直接下载，store 方法可以保存
        Excel::store(new ExcelExport($data), 'statementWriter_'.$this->date.'.xlsx');
    }

    private function statementData(){
        $time = $this->date;
        $dailyData = EconomyDailyService::daydata($time);
        $data = array(
            "electricity"=>array(
                "power"=>array(
                    "no1turbine"=>array( //1#机发电量
                        "today"=>$dailyData['no1_electric_energy']['value'],
                        "month"=>$dailyData['no1_electric_energy@total']['value']
                    ),
                    "no2turbine"=>array( //2#机发电量
                        "today"=>$dailyData['no2_electric_energy']['value'],
                        "month"=>$dailyData['no2_electric_energy@total']['value']
                    ),
                    "total"=>array( //合计
                        "today"=>($dailyData['no1_electric_energy']['value'] + $dailyData['no2_electric_energy']['value']),
                        "month"=>($dailyData['no1_electric_energy@total']['value'] + $dailyData['no2_electric_energy@total']['value'])
                    )
                ),
                "factory_use_electricity"=>array( //厂用电量
                    "today"=>$dailyData['factory_use_electricity']['value'],
                    "month"=>$dailyData['factory_use_electricity@total']['value']
                ),
                "no1turbine_online_electricity"=>array( //1#机上网电量
                    "today"=>$dailyData['no1_online_electric_energy']['value'],
                    "month"=>$dailyData['no1_online_electric_energy@total']['value']
                ),
                "no2turbine_online_electricity"=>array( //2#机上网电量
                    "today"=>$dailyData['no2_online_electric_energy']['value'],
                    "month"=>$dailyData['no2_online_electric_energy@total']['value']
                ),
                "total_online_electricity"=>array( //总计上网电量
                    "today"=>$dailyData['no1_online_electric_energy']['value'] + $dailyData['no2_online_electric_energy']['value'],
                    "month"=>$dailyData['no1_online_electric_energy@total']['value'] + $dailyData['no2_online_electric_energy@total']['value']
                ),
                "factory_use_electricity_rate"=>array( //厂用电率
                    "today"=>$dailyData['factory_use_electricity_rate']['value'],
                    "month"=>$dailyData['factory_use_electricity_rate_total']['value']
                ),
                "buy_electricity"=>array( //外购电量
                    "today"=>$dailyData['out_buy_electricity']['value'],
                    "month"=>$dailyData['out_buy_electricity@total']['value']
                ),
                "leachate_station_use_electricity"=>array( //渗沥水处理站用电量
                    "today"=>$dailyData['leachate_station_use_electricity']['value'],
                    "month"=>$dailyData['leachate_station_use_electricity@total']['value']
                ),
                "give_first_factory_electricity"=>array( //向一期供电
                    "today"=>$dailyData['supply_first_factory_electricity']['value'],
                    "month"=>$dailyData['supply_first_factory_electricity@total']['value']
                ),
                "ton_rubbish_produce_electricity"=>array( //吨垃圾发电量
                    "today"=>$dailyData['ton_rubbish_electricity']['value'],
                    "month"=>$dailyData['ton_rubbish_electricity_total']['value']
                ),
                "ton_rubbish_online_electricity"=>array( //吨垃圾上网电量
                    "today"=>$dailyData['ton_rubbish_online_electricity']['value'],
                    "month"=>$dailyData['ton_rubbish_online_electricity_total']['value']
                ),
            ),
            "consume"=>array(
                "buy_water"=>array( //外购水量
                    "today"=>$dailyData['out_buy_water']['value'],
                    "month"=>$dailyData['out_buy_water@total']['value']
                ),
                "boiler_use_water"=>array( //锅炉用水量
                    "today"=>$dailyData['boiler_use_water']['value'],
                    "month"=>$dailyData['boiler_use_water@total']['value']
                ),
                "use_oil"=>array( //燃油耗量
                    "today"=>$dailyData['use_oil']['value'],
                    "month"=>$dailyData['use_oil@total']['value']
                ),
                "use_cement"=>array( //水泥用量
                    "today"=>$dailyData['use_cement']['value'],
                    "month"=>$dailyData['use_cement@total']['value']
                ),
                "supplement_water_rate"=>array( //补水率
                    "today"=>$dailyData['supply_water_rate']['value'],
                    "month"=>$dailyData['supply_water_rate_total']['value']
                ),
                "lime"=>array( //石灰耗量
                    "today"=>$dailyData['lime_use']['value'],
                    "month"=>$dailyData['lime_use_total']['value']
                ),
                "carbon"=>array( //活性炭耗量
                    "today"=>$dailyData['active_carbon']['value'],
                    "month"=>$dailyData['active_carbon_total']['value']
                )
            ),
            "incineration"=>array(
                "life_rubbish_entry"=>array( //生活垃圾进库量
                    "today"=>$dailyData['life_rubbish_entry']['value'],
                    "month"=>$dailyData['life_rubbish_entry@total']['value']
                ),
                "incineration_rubbish"=>array( //垃圾焚烧量
                    "today"=>$dailyData['rubbish_incineration']['value'],
                    "month"=>$dailyData['rubbish_incineration@total']['value']
                )
            ),
            "sewage"=>array(
                "out_transport_sewage"=>array( //污水外运量
                    "today"=>$dailyData['waste_water_out_transport']['value'],
                    "month"=>$dailyData['waste_water_out_transport@total']['value']
                ),
                "sewage_station_handle_sewage"=>array( //污水站污水处理量
                    "today"=>$dailyData['water_station_handle']['value'],
                    "month"=>$dailyData['water_station_handle@total']['value']
                ),
                "sewage_station_produce_water"=>array( //污水站污水出水量
                    "today"=>$dailyData['water_station_produce']['value'],
                    "month"=>$dailyData['water_station_produce@total']['value']
                ),
                "sewage_station_produce_water_cod"=>array( //污水站污水出水指标
                    "today"=>$dailyData['water_station_cod']['value'],
                    "month"=>'/'
                )
            ),
            "turbine_run_kpi"=>array(
                "run_time"=>array( //运行时间
                    "no1turbine"=>$dailyData['no1turbine_run_time']['value'],
                    "no2turbine"=>$dailyData['no2turbine_run_time']['value'],
                    "standard"=>'~'
                ),
                "run_time_total"=>array( //运行时间累计
                    "no1turbine"=>$dailyData['no1turbine_run_time@total']['value'],
                    "no2turbine"=>$dailyData['no2turbine_run_time@total']['value'],
                    "standard"=>'~'
                ),
                "top_load"=>array( //最高负荷
                    "no1turbine"=>$dailyData['no1turbine@load']['max'],
                    "no2turbine"=>$dailyData['no2turbine@load']['max'],
                    "standard"=>'~'
                ),
                "average_load"=>array( //平均负荷
                    "no1turbine"=>$dailyData['no1turbine@load']['value'],
                    "no2turbine"=>$dailyData['no2turbine@load']['value'],
                    "standard"=>'~'
                ),
                "entry_steam_pressure"=>array( //进汽压力
                    "no1turbine"=>$dailyData['no1turbine@entry_steam_pressure']['min'].'~'.$dailyData['no1turbine@entry_steam_pressure']['max'],
                    "no2turbine"=>$dailyData['no2turbine@entry_steam_pressure']['min'].'~'.$dailyData['no2turbine@entry_steam_pressure']['max'],
                    "standard"=>'~'
                ),
                "entry_steam_temperature"=>array( //进汽温度
                    "no1turbine"=>$dailyData['no1turbine@entry_steam_temperature']['min'].'~'.$dailyData['no1turbine@entry_steam_temperature']['max'],
                    "no2turbine"=>$dailyData['no2turbine@entry_steam_temperature']['min'].'~'.$dailyData['no2turbine@entry_steam_temperature']['max'],
                    "standard"=>'~'
                ),
                "out_steam_temperature"=>array( //排汽温度
                    "no1turbine"=>$dailyData['no1turbine@out_steam_temperature']['min'].'~'.$dailyData['no1turbine@out_steam_temperature']['max'],
                    "no2turbine"=>$dailyData['no2turbine@out_steam_temperature']['min'].'~'.$dailyData['no2turbine@out_steam_temperature']['max'],
                    "standard"=>'~'
                ),
                "entry_steam_flow"=>array( //进汽流量
                    "no1turbine"=>$dailyData['no1turbine@entry_steam_flow']['value'],
                    "no2turbine"=>$dailyData['no2turbine@entry_steam_flow']['value'],
                    "standard"=>'~'
                ),
                "entry_steam_flow_total"=>array( //进汽流量累计
                    "no1turbine"=>$dailyData['no1turbine@entry_steam_flow@total']['value'],
                    "no2turbine"=>$dailyData['no2turbine@entry_steam_flow@total']['value'],
                    "standard"=>'~'
                ),
                "steam_rate"=>array( //汽耗率
                    "no1turbine"=>$dailyData['no1turbine_steam_rate']['value'],
                    "no2turbine"=>$dailyData['no2turbine_steam_rate']['value'],
                    "standard"=>'~'
                ),
                "vacuum"=>array( //真空度
                    "no1turbine"=>$dailyData['no1turbine@vacuum']['value'],
                    "no2turbine"=>$dailyData['no2turbine@vacuum']['value'],
                    "standard"=>'~'
                ),
            ),
            "boiler_run_kpi"=>array(
                "run_time"=>array( //运行时间
                    "no1boiler"=>$dailyData['no1boiler_run_time']['value'],
                    "no2boiler"=>$dailyData['no2boiler_run_time']['value'],
                    "no3boiler"=>$dailyData['no3boiler_run_time']['value'],
                    "standard"=>'~'
                ),
                "steam_flow"=>array( //蒸汽流量
                    "no1boiler"=>$dailyData['no1boiler@steam_flow']['value'],
                    "no2boiler"=>$dailyData['no2boiler@steam_flow']['value'],
                    "no3boiler"=>$dailyData['no1boiler@steam_flow']['value'],
                    "standard"=>'~'
                ),
                "average_load"=>array( //平均流量
                    "no1boiler"=>$dailyData['no1boiler@steam_load']['value'],
                    "no2boiler"=>$dailyData['no2boiler@steam_load']['value'],
                    "no3boiler"=>$dailyData['no1boiler@steam_load']['value'],
                    "standard"=>'~'
                ),
                "flow_total"=>array( //流量累计
                    "no1boiler"=>$dailyData['no1boiler@steam_flow@total']['value'],
                    "no2boiler"=>$dailyData['no2boiler@steam_flow@total']['value'],
                    "no3boiler"=>$dailyData['no1boiler@steam_flow@total']['value'],
                    "standard"=>'~'
                ),
                "hearth_pressure"=>array( //炉膛负压
                    "no1boiler"=>$dailyData['no1boiler@under_pressure']['min'].'~'.$dailyData['no1boiler@under_pressure']['max'],
                    "no2boiler"=>$dailyData['no2boiler@under_pressure']['min'].'~'.$dailyData['no2boiler@under_pressure']['max'],
                    "no3boiler"=>$dailyData['no1boiler@under_pressure']['min'].'~'.$dailyData['no1boiler@under_pressure']['max'],
                    "standard"=>'~'
                ),
                "give_water_temperature"=>array( //给水温度
                    "no1boiler"=>$dailyData['no1boiler@give_water_temperatue']['min'].'~'.$dailyData['no1boiler@give_water_temperatue']['max'],
                    "no2boiler"=>$dailyData['no2boiler@give_water_temperatue']['min'].'~'.$dailyData['no2boiler@give_water_temperatue']['max'],
                    "no3boiler"=>$dailyData['no1boiler@give_water_temperatue']['min'].'~'.$dailyData['no1boiler@give_water_temperatue']['max'],
                    "standard"=>'~'
                ),
                "first_wind_temperature"=>array( //一次风温
                    "no1boiler"=>$dailyData['no1boiler@first_wind_temperature']['min'].'~'.$dailyData['no1boiler@first_wind_temperature']['max'],
                    "no2boiler"=>$dailyData['no2boiler@first_wind_temperature']['min'].'~'.$dailyData['no2boiler@first_wind_temperature']['max'],
                    "no3boiler"=>$dailyData['no1boiler@first_wind_temperature']['min'].'~'.$dailyData['no1boiler@first_wind_temperature']['max'],
                    "standard"=>'~'
                ),
                "superheated_steam_temperature"=>array( //过热蒸汽气温
                    "no1boiler"=>$dailyData['no1boiler@hot_steam_temperature']['min'].'~'.$dailyData['no1boiler@hot_steam_temperature']['max'],
                    "no2boiler"=>$dailyData['no2boiler@hot_steam_temperature']['min'].'~'.$dailyData['no2boiler@hot_steam_temperature']['max'],
                    "no3boiler"=>$dailyData['no1boiler@hot_steam_temperature']['min'].'~'.$dailyData['no1boiler@hot_steam_temperature']['max'],
                    "standard"=>"~"
                ),
                "exit_gas_temperature"=>array( //排烟温度
                    "no1boiler"=>$dailyData['no1boiler@out_gas_temperature']['min'].'~'.$dailyData['no1boiler@out_gas_temperature']['max'],
                    "no2boiler"=>$dailyData['no2boiler@out_gas_temperature']['min'].'~'.$dailyData['no2boiler@out_gas_temperature']['max'],
                    "no3boiler"=>$dailyData['no1boiler@out_gas_temperature']['min'].'~'.$dailyData['no1boiler@out_gas_temperature']['max'],
                    "standard"=>'~'
                ),
                "hop_pocket_entry_temperature"=>array( //布袋进口温度
                    "no1boiler"=>$dailyData['no1boiler@pocket_entry_gas_temperature']['min'].'~'.$dailyData['no1boiler@pocket_entry_gas_temperature']['max'],
                    "no2boiler"=>$dailyData['no2boiler@pocket_entry_gas_temperature']['min'].'~'.$dailyData['no2boiler@pocket_entry_gas_temperature']['max'],
                    "no3boiler"=>$dailyData['no1boiler@pocket_entry_gas_temperature']['min'].'~'.$dailyData['no1boiler@pocket_entry_gas_temperature']['max'],
                    "standard"=>'~'
                ),
                "top_hearth_temperature"=>array( //最高炉膛温度
                    "no1boiler"=>$dailyData['no1boiler@inner_temperature']['max'],
                    "no2boiler"=>$dailyData['no2boiler@inner_temperature']['max'],
                    "no3boiler"=>$dailyData['no1boiler@inner_temperature']['max'],
                    "standard"=>'~'
                ),
                "bottom_hearth_temperature"=>array( //最低炉膛温度
                    "no1boiler"=>$dailyData['no1boiler@inner_temperature']['min'],
                    "no2boiler"=>$dailyData['no2boiler@inner_temperature']['min'],
                    "no3boiler"=>$dailyData['no1boiler@inner_temperature']['min'],
                    "standard"=>'~'
                )
            ),
            "boiler_water_kpi"=>array(
                "ph"=>array( //PH值
                    "no1boiler"=>$dailyData['no1boiler_ph_value']['value'],
                    "no2boiler"=>$dailyData['no2boiler_ph_value']['value'],
                    "no3boiler"=>$dailyData['no1boiler_ph_value']['value'],
                    "standard"=>'~'
                ),
                "phosphoric_acid"=>array( //磷酸根
                    "no1boiler"=>$dailyData['no1boiler_phosphate_radical']['value'],
                    "no2boiler"=>$dailyData['no2boiler_phosphate_radical']['value'],
                    "no3boiler"=>$dailyData['no1boiler_phosphate_radical']['value'],
                    "standard"=>'~'
                )
            ),
            "production_about"=>array(
                // "longwang"=>$dailyData['no1boiler_phosphate_radical']['value'],
                // "oujiangkou"=>$dailyData['no1boiler_phosphate_radical']['value'],
                // "economic_development"=>$dailyData['no1boiler_phosphate_radical']['value'],
                // "ouhai"=>$dailyData['no1boiler_phosphate_radical']['value'],
                // "many_years_rubbish"=>$dailyData['no1boiler_phosphate_radical']['value'],
                // "dongtou"=>$dailyData['no1boiler_phosphate_radical']['value'],
                // "lucheng"=>$dailyData['no1boiler_phosphate_radical']['value'],
                // "food_rubbish"=>$dailyData['no1boiler_phosphate_radical']['value'],
                // "yongjia"=>$dailyData['yongjia']['value'],
                // "cangnan"=>$dailyData['cangnan']['value'],
                // "longgang"=>$dailyData['longgang']['value'],
                // "yueqing"=>$dailyData['yueqing']['value'],
                // "pingyang"=>$dailyData['pingyang']['value'],
                // "wencheng"=>$dailyData['wencheng']['value'],
                // "j9898"=>$dailyData['j9898']['value'],
                // "lc_many_years_rubbish"=>$dailyData['lc_many_years_rubbish']['value'],
                // "longwang_yongzhong"=>$dailyData['longwang_yongzhong']['value'],
                // "chuyu"=>$dailyData['chuyu']['value'],
                //"summary"=>$dailyData['summary']['value']
            ),
            "sign" => "生技科签字 林贵  ".date('Y年m月d日')
        );
        $result = [];
        $dataRow1 = [
            'acell' => '总指标',
            'bcell' => '发电指标',
            'ccell' => '发电量',
            'dcell' => '1#机',
            'ecell' => 'kwh',
            'fcell' => $data['electricity']['power']['no1turbine']['today'],
            'gcell' => $data['electricity']['power']['no1turbine']['month'],
            'hcell' => '汽机运行指标',
            'icell' => '运行时间',
            'jcell' => '小时',
            'kcell' => $data['turbine_run_kpi']['run_time']['no1turbine'],
            'lcell' => '',
            'mcell' => '',
            'ncell' => $data['turbine_run_kpi']['run_time']['no2turbine'],
            'ocell' => '',
            'pcell' => '',
            'qcell' => $data['turbine_run_kpi']['run_time']['standard']
        ];
        $result[] = $dataRow1;
        $dataRow2 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '',
            'dcell' => '2#机',
            'ecell' => 'kwh',
            'fcell' => $data['electricity']['power']['no2turbine']['today'],
            'gcell' => $data['electricity']['power']['no2turbine']['month'],
            'hcell' => '',
            'icell' => '运行时间累计',
            'jcell' => '小时',
            'kcell' => $data['turbine_run_kpi']['run_time_total']['no1turbine'],
            'lcell' => '',
            'mcell' => '',
            'ncell' => $data['turbine_run_kpi']['run_time_total']['no2turbine'],
            'ocell' => '',
            'pcell' => '',
            'qcell' => $data['turbine_run_kpi']['run_time_total']['standard']
        ];
        $result[] = $dataRow2;
        $dataRow3 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '',
            'dcell' => '合计',
            'ecell' => 'kwh',
            'fcell' => $data['electricity']['power']['total']['today'],
            'gcell' => $data['electricity']['power']['total']['month'],
            'hcell' => '',
            'icell' => '最高负荷',
            'jcell' => 'kw',
            'kcell' => $data['turbine_run_kpi']['top_load']['no1turbine'],
            'lcell' => '',
            'mcell' => '',
            'ncell' => $data['turbine_run_kpi']['top_load']['no2turbine'],
            'ocell' => '',
            'pcell' => '',
            'qcell' => $data['turbine_run_kpi']['top_load']['standard']
        ];
        $result[] = $dataRow3;
        $dataRow4 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '厂用电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => $data['electricity']['factory_use_electricity']['today'],
            'gcell' => $data['electricity']['factory_use_electricity']['month'],
            'hcell' => '',
            'icell' => '平均负荷',
            'jcell' => 'kw',
            'kcell' => $data['turbine_run_kpi']['average_load']['no1turbine'],
            'lcell' => '',
            'mcell' => '',
            'ncell' => $data['turbine_run_kpi']['average_load']['no2turbine'],
            'ocell' => '',
            'pcell' => '',
            'qcell' => $data['turbine_run_kpi']['average_load']['standard']
        ];
        $result[] = $dataRow4;
        $dataRow5 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '1#机上网电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => $data['electricity']['no1turbine_online_electricity']['today'],
            'gcell' => $data['electricity']['no1turbine_online_electricity']['month'],
            'hcell' => '',
            'icell' => '进汽压力',
            'jcell' => 'MPa',
            'kcell' => $data['turbine_run_kpi']['entry_steam_pressure']['no1turbine'],
            'lcell' => '',
            'mcell' => '',
            'ncell' => $data['turbine_run_kpi']['entry_steam_pressure']['no2turbine'],
            'ocell' => '',
            'pcell' => '',
            'qcell' => $data['turbine_run_kpi']['entry_steam_pressure']['standard']
        ];
        $result[] = $dataRow5;
        $dataRow6 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '2#机上网电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => $data['electricity']['no2turbine_online_electricity']['today'],
            'gcell' => $data['electricity']['no2turbine_online_electricity']['month'],
            'hcell' => '',
            'icell' => '进汽温度',
            'jcell' => '℃',
            'kcell' => $data['turbine_run_kpi']['entry_steam_temperature']['no1turbine'],
            'lcell' => '',
            'mcell' => '',
            'ncell' => $data['turbine_run_kpi']['entry_steam_temperature']['no2turbine'],
            'ocell' => '',
            'pcell' => '',
            'qcell' => $data['turbine_run_kpi']['entry_steam_temperature']['standard']
        ];
        $result[] = $dataRow6;
        $dataRow7 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '总计上网电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => $data['electricity']['total_online_electricity']['today'],
            'gcell' => $data['electricity']['total_online_electricity']['month'],
            'hcell' => '',
            'icell' => '排汽温度',
            'jcell' => '℃',
            'kcell' => $data['turbine_run_kpi']['out_steam_temperature']['no1turbine'],
            'lcell' => '',
            'mcell' => '',
            'ncell' => $data['turbine_run_kpi']['out_steam_temperature']['no2turbine'],
            'ocell' => '',
            'pcell' => '',
            'qcell' => $data['turbine_run_kpi']['out_steam_temperature']['standard']
        ];
        $result[] = $dataRow7;
        $dataRow8 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '厂用电率',
            'dcell' => '',
            'ecell' => '%',
            'fcell' => $data['electricity']['factory_use_electricity_rate']['today'],
            'gcell' => $data['electricity']['factory_use_electricity_rate']['month'],
            'hcell' => '',
            'icell' => '进汽流量',
            'jcell' => '吨',
            'kcell' => $data['turbine_run_kpi']['entry_steam_flow']['no1turbine'],
            'lcell' => '',
            'mcell' => '',
            'ncell' => $data['turbine_run_kpi']['entry_steam_flow']['no2turbine'],
            'ocell' => '',
            'pcell' => '',
            'qcell' => $data['turbine_run_kpi']['entry_steam_flow']['standard']
        ];
        $result[] = $dataRow8;
        $dataRow9 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '外购电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => $data['electricity']['buy_electricity']['today'],
            'gcell' => $data['electricity']['buy_electricity']['month'],
            'hcell' => '',
            'icell' => '进汽流量累计',
            'jcell' => '吨',
            'kcell' => $data['turbine_run_kpi']['entry_steam_flow_total']['no1turbine'],
            'lcell' => '',
            'mcell' => '',
            'ncell' => $data['turbine_run_kpi']['entry_steam_flow_total']['no2turbine'],
            'ocell' => '',
            'pcell' => '',
            'qcell' => $data['turbine_run_kpi']['entry_steam_flow_total']['standard']
        ];
        $result[] = $dataRow9;
        $dataRow10 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '渗沥水处理站用电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => $data['electricity']['leachate_station_use_electricity']['today'],
            'gcell' => $data['electricity']['leachate_station_use_electricity']['month'],
            'hcell' => '',
            'icell' => '汽耗率',
            'jcell' => 'kg/度',
            'kcell' => $data['turbine_run_kpi']['steam_rate']['no1turbine'],
            'lcell' => '',
            'mcell' => '',
            'ncell' => $data['turbine_run_kpi']['steam_rate']['no2turbine'],
            'ocell' => '',
            'pcell' => '',
            'qcell' => $data['turbine_run_kpi']['steam_rate']['standard']
        ];
        $result[] = $dataRow10;
        $dataRow11 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '一期供电',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => $data['electricity']['give_first_factory_electricity']['today'],
            'gcell' => $data['electricity']['give_first_factory_electricity']['month'],
            'hcell' => '',
            'icell' => '真空度',
            'jcell' => 'MPa',
            'kcell' => $data['turbine_run_kpi']['vacuum']['no1turbine'],
            'lcell' => '',
            'mcell' => '',
            'ncell' => $data['turbine_run_kpi']['vacuum']['no2turbine'],
            'ocell' => '',
            'pcell' => '',
            'qcell' => $data['turbine_run_kpi']['vacuum']['standard']
        ];
        $result[] = $dataRow11;
        $dataRow12 = [
            'acell' => '',
            'bcell' => '消耗指标',
            'ccell' => '吨垃圾发电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => $data['electricity']['ton_rubbish_produce_electricity']['today'],
            'gcell' => $data['electricity']['ton_rubbish_produce_electricity']['month'],
            'hcell' => '锅炉运行指标',
            'icell' => '项目',
            'jcell' => '单位',
            'kcell' => '1#炉',
            'lcell' => '',
            'mcell' => '2#炉',
            'ncell' => '',
            'ocell' => '3#炉',
            'pcell' => '',
            'qcell' => '标准值'
        ];
        $result[] = $dataRow12;
        $dataRow13 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '吨垃圾上网电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => $data['electricity']['ton_rubbish_online_electricity']['today'],
            'gcell' => $data['electricity']['ton_rubbish_online_electricity']['month'],
            'hcell' => '',
            'icell' => '运行时间',
            'jcell' => '小时',
            'kcell' => $data['boiler_run_kpi']['run_time']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_run_kpi']['run_time']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_run_kpi']['run_time']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_run_kpi']['run_time']['standard']
        ];
        $result[] = $dataRow13;
        $dataRow14 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '外购水量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => $data['consume']['buy_water']['today'],
            'gcell' => $data['consume']['buy_water']['month'],
            'hcell' => '',
            'icell' => '蒸汽流量',
            'jcell' => '吨',
            'kcell' => $data['boiler_run_kpi']['steam_flow']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_run_kpi']['steam_flow']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_run_kpi']['steam_flow']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_run_kpi']['steam_flow']['standard']
        ];
        $result[] = $dataRow14;
        $dataRow15 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '其中锅炉用水量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => $data['consume']['boiler_use_water']['today'],
            'gcell' => $data['consume']['boiler_use_water']['month'],
            'hcell' => '',
            'icell' => '平均流量',
            'jcell' => '吨',
            'kcell' => $data['boiler_run_kpi']['average_load']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_run_kpi']['average_load']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_run_kpi']['average_load']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_run_kpi']['average_load']['standard']
        ];
        $result[] = $dataRow15;
        $dataRow16 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '补水率',
            'dcell' => '',
            'ecell' => '%',
            'fcell' => $data['consume']['supplement_water_rate']['today'],
            'gcell' => $data['consume']['supplement_water_rate']['month'],
            'hcell' => '',
            'icell' => '流量累计',
            'jcell' => '吨',
            'kcell' => $data['boiler_run_kpi']['flow_total']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_run_kpi']['flow_total']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_run_kpi']['flow_total']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_run_kpi']['flow_total']['standard']
        ];
        $result[] = $dataRow16;
        $dataRow17 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '燃油耗量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => $data['consume']['use_oil']['today'],
            'gcell' => $data['consume']['use_oil']['month'],
            'hcell' => '',
            'icell' => '炉膛负压',
            'jcell' => 'Pa-Pa',
            'kcell' => $data['boiler_run_kpi']['hearth_pressure']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_run_kpi']['hearth_pressure']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_run_kpi']['hearth_pressure']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_run_kpi']['hearth_pressure']['standard']
        ];
        $result[] = $dataRow17;
        $dataRow18 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '石灰耗量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => $data['consume']['lime']['today'],
            'gcell' => $data['consume']['lime']['month'],
            'hcell' => '',
            'icell' => '给水温度',
            'jcell' => '℃-℃',
            'kcell' => $data['boiler_run_kpi']['give_water_temperature']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_run_kpi']['give_water_temperature']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_run_kpi']['give_water_temperature']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_run_kpi']['give_water_temperature']['standard']
        ];
        $result[] = $dataRow18;
        $dataRow19 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '水泥耗量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => $data['consume']['use_cement']['today'],
            'gcell' => $data['consume']['use_cement']['month'],
            'hcell' => '',
            'icell' => '一次风温',
            'jcell' => '℃-℃',
            'kcell' => $data['boiler_run_kpi']['first_wind_temperature']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_run_kpi']['first_wind_temperature']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_run_kpi']['first_wind_temperature']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_run_kpi']['first_wind_temperature']['standard']
        ];
        $result[] = $dataRow19;
        $dataRow20 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '活性炭耗量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => $data['consume']['carbon']['today'],
            'gcell' => $data['consume']['carbon']['month'],
            'hcell' => '',
            'icell' => '过热蒸汽风温',
            'jcell' => '℃-℃',
            'kcell' => $data['boiler_run_kpi']['superheated_steam_temperature']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_run_kpi']['superheated_steam_temperature']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_run_kpi']['superheated_steam_temperature']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_run_kpi']['superheated_steam_temperature']['standard']
        ];
        $result[] = $dataRow20;
        $dataRow21 = [
            'acell' => '',
            'bcell' => '燃烧指标',
            'ccell' => '垃圾进库量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => $data['incineration']['life_rubbish_entry']['today'],
            'gcell' => $data['incineration']['life_rubbish_entry']['month'],
            'hcell' => '',
            'icell' => '排烟温度',
            'jcell' => '℃-℃',
            'kcell' => $data['boiler_run_kpi']['exit_gas_temperature']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_run_kpi']['exit_gas_temperature']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_run_kpi']['exit_gas_temperature']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_run_kpi']['exit_gas_temperature']['standard']
        ];
        $result[] = $dataRow21;
        $dataRow22 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '垃圾焚烧量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => $data['incineration']['incineration_rubbish']['today'],
            'gcell' => $data['incineration']['incineration_rubbish']['month'],
            'hcell' => '',
            'icell' => '布袋进口烟温',
            'jcell' => '℃-℃',
            'kcell' => $data['boiler_run_kpi']['hop_pocket_entry_temperature']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_run_kpi']['hop_pocket_entry_temperature']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_run_kpi']['hop_pocket_entry_temperature']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_run_kpi']['hop_pocket_entry_temperature']['standard']
        ];
        $result[] = $dataRow22;
        $dataRow23 = [
            'acell' => '',
            'bcell' => '污水指标',
            'ccell' => '污水处理量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => $data['sewage']['out_transport_sewage']['today'],
            'gcell' => $data['sewage']['out_transport_sewage']['month'],
            'hcell' => '',
            'icell' => '最高炉膛温度',
            'jcell' => '℃',
            'kcell' => $data['boiler_run_kpi']['top_hearth_temperature']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_run_kpi']['top_hearth_temperature']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_run_kpi']['top_hearth_temperature']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_run_kpi']['top_hearth_temperature']['standard']
        ];
        $result[] = $dataRow23;
        $dataRow24 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '污水站污水处理量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => $data['sewage']['sewage_station_handle_sewage']['today'],
            'gcell' => $data['sewage']['sewage_station_handle_sewage']['month'],
            'hcell' => '',
            'icell' => '最低炉膛温度',
            'jcell' => '℃',
            'kcell' => $data['boiler_run_kpi']['bottom_hearth_temperature']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_run_kpi']['bottom_hearth_temperature']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_run_kpi']['bottom_hearth_temperature']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_run_kpi']['bottom_hearth_temperature']['standard']
        ];
        $result[] = $dataRow24;
        $dataRow25 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '污水站污水出水量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => $data['sewage']['sewage_station_produce_water']['today'],
            'gcell' => $data['sewage']['sewage_station_produce_water']['month'],
            'hcell' => '炉水指标',
            'icell' => 'PH值',
            'jcell' => '/',
            'kcell' => $data['boiler_water_kpi']['ph']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_water_kpi']['ph']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_water_kpi']['ph']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_water_kpi']['ph']['standard']
        ];
        $result[] = $dataRow25;
        $dataRow26 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '污水出水COD指标',
            'dcell' => '',
            'ecell' => 'mg/L',
            'fcell' => $data['sewage']['sewage_station_produce_water_cod']['today'],
            'gcell' => $data['sewage']['sewage_station_produce_water_cod']['month'],
            'hcell' => '',
            'icell' => '磷酸根',
            'jcell' => 'mg/L',
            'kcell' => $data['boiler_water_kpi']['phosphoric_acid']['no1boiler'],
            'lcell' => '',
            'mcell' => $data['boiler_water_kpi']['phosphoric_acid']['no2boiler'],
            'ncell' => '',
            'ocell' => $data['boiler_water_kpi']['phosphoric_acid']['no3boiler'],
            'pcell' => '',
            'qcell' => $data['boiler_water_kpi']['phosphoric_acid']['standard']
        ];
        $result[] = $dataRow26;
        $text = "生产情况简要说明："."\r\n";

        $dataRow27 = [
            'acell' => $text,
            'bcell' => '',
            'ccell' => '',
            'dcell' => '',
            'ecell' => '',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '',
            'jcell' => '',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => ''
        ];
        //$result[] = $dataRow27;
        $dataRow28 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '',
            'dcell' => '',
            'ecell' => '',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '',
            'jcell' => '',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        //$result[] = $dataRow28;
        $dataRow29 = [
            'acell' => $data['sign'],
            'bcell' => '',
            'ccell' => '',
            'dcell' => '',
            'ecell' => '',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '',
            'jcell' => '',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => ''
        ];
        //$result[] = $dataRow29;

        $titles = ['温州龙湾伟明环保能源有限公司技术经济指标日报表'];
        $date = [date('Y年m月d日')];
        $headings = ['项目', '', '', '', '单位', '本日', '本月累计', '项目', '', '单位', '1#机', '', '', '2#机',  '', '', '标准值'];
        array_unshift($result, $titles, $date, $headings);

        return $result;
    }
}
