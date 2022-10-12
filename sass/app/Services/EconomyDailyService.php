<?php

namespace App\Services;
use App\Repositories\ElectricityDayDataRepository;
use App\Repositories\GrabGarbageDayDataReposotory;
use App\Repositories\WeighBridgeDayDataReposotory;

class EconomyDailyService{
    public function daydata($date, $tenement_conn, $factory)
    {
        $final = [];
        $date_start = $date;
        $date_end = $date;
        $month_start = date('Y-m', strtotime($date)) . '-01';
        $month_end = $date;
        $electricityObj = new ElectricityDayDataRepository();
        $weighBridgeObj = new WeighBridgeDayDataReposotory();
        $grabGarbageObj = new GrabGarbageDayDataReposotory();
        $sign_name = "林贵";
        $data = array(
            //发电指标
            "electricity"=>array(
                //发电量
                "power"=>array(
                    "no1turbine"=>array( //1#机发电量
                        "today"=>0,//本日累计
                        "month"=>0,//本月累计
                    ),
                    "no2turbine"=>array( //2#机发电量
                        "today"=>0,
                        "month"=>0,
                    ),
                    "total"=>array( //合计
                        "today"=>0,
                        "month"=>0,
                    )
                ),
                "factory_use_electricity"=>array( //厂用电量
                    "today"=>0,
                    "month"=>0,
                ),
                "no1turbine_online_electricity"=>array( //1#机上网电量
                    "today"=>0,
                    "month"=>0,
                ),
                "no2turbine_online_electricity"=>array( //2#机上网电量
                    "today"=>0,
                    "month"=>0,
                ),
                "total_online_electricity"=>array( //总计上网电量
                    "today"=>0,
                    "month"=>0,
                ),
                "factory_use_electricity_rate"=>array( //厂用电率
                    "today"=>0,
                    "month"=>0,
                ),
                "buy_electricity"=>array( //外购电量
                    "today"=>0,
                    "month"=>0,
                ),
                "leachate_station_use_electricity"=>array( //渗沥水处理站用电量
                    "today"=>0,
                    "month"=>0,
                ),
                "give_first_factory_electricity"=>array( //向一期供电
                    "today"=>0,
                    "month"=>0,
                ),
                "ton_rubbish_produce_electricity"=>array( //吨垃圾发电量
                    "today"=>0,
                    "month"=>0,
                ),
                "ton_rubbish_online_electricity"=>array( //吨垃圾上网电量
                    "today"=>0,
                    "month"=>0,
                ),
            ),
            //消耗指标
            "consume"=>array(
                "buy_water"=>array( //外购水量
                    "today"=>0,
                    "month"=>0,
                ),
                "boiler_use_water"=>array( //锅炉用水量
                    "today"=>0,
                    "month"=>0,
                ),
                "use_oil"=>array( //燃油耗量
                    "today"=>0,
                    "month"=>0,
                ),
                "use_cement"=>array( //水泥用量
                    "today"=>0,
                    "month"=>0,
                ),
                "supplement_water_rate"=>array( //补水率
                    "today"=>0,
                    "month"=>0,
                ),
                "lime"=>array( //石灰耗量
                    "today"=>0,
                    "month"=>0,
                ),
                "carbon"=>array( //活性炭耗量
                    "today"=>0,
                    "month"=>0,
                )
            ),
            //燃烧指标
            "incineration"=>array(
                "life_rubbish_entry"=>array( //生活垃圾进库量
                    "today"=>0,
                    "month"=>0,
                ),
                "incineration_rubbish"=>array( //垃圾焚烧量
                    "today"=>0,
                    "month"=>0,
                )
            ),
            //污水指标
            "sewage"=>array(
                "out_transport_sewage"=>array( //污水外运量
                    "today"=>0,
                    "month"=>0,
                ),
                "sewage_station_handle_sewage"=>array( //污水站污水处理量
                    "today"=>0,
                    "month"=>0,
                ),
                "sewage_station_produce_water"=>array( //污水站污水出水量
                    "today"=>0,
                    "month"=>0,
                ),
                "sewage_station_produce_water_cod"=>array( //污水站污水出水指标
                    "today"=>0,
                    "month"=>'/'
                )
            ),
            //汽机运行指标
            "turbine_run_kpi"=>array(
                "run_time"=>array( //运行时间
                    "no1turbine"=>0,
                    "no2turbine"=>0,
                    "standard"=>'~'
                ),
                "run_time_total"=>array( //运行时间累计
                    "no1turbine"=>0,
                    "no2turbine"=>0,
                    "standard"=>'~'
                ),
                "top_load"=>array( //最高负荷
                    "no1turbine"=>0,
                    "no2turbine"=>0,
                    "standard"=>'~'
                ),
                "average_load"=>array( //平均负荷
                    "no1turbine"=>0,
                    "no2turbine"=>0,
                    "standard"=>'~'
                ),
                "entry_steam_pressure"=>array( //进汽压力
                    "no1turbine"=>0,
                    "no2turbine"=>0,
                    "standard"=>'~'
                ),
                "entry_steam_temperature"=>array( //进汽温度
                    "no1turbine"=>0,
                    "no2turbine"=>0,
                    "standard"=>'~'
                ),
                "out_steam_temperature"=>array( //排汽温度
                    "no1turbine"=>0,
                    "no2turbine"=>0,
                    "standard"=>'~'
                ),
                "entry_steam_flow"=>array( //进汽流量
                    "no1turbine"=>0,
                    "no2turbine"=>0,
                    "standard"=>'~'
                ),
                "entry_steam_flow_total"=>array( //进汽流量累计
                    "no1turbine"=>0,
                    "no2turbine"=>0,
                    "standard"=>'~'
                ),
                "steam_rate"=>array( //汽耗率
                    "no1turbine"=>0,
                    "no2turbine"=>0,
                    "standard"=>'~'
                ),
                "vacuum"=>array( //真空度
                    "no1turbine"=>0,
                    "no2turbine"=>0,
                    "standard"=>'~'
                ),
            ),
            //锅炉运行指标
            "boiler_run_kpi"=>array(
                "run_time"=>array( //运行时间
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>'~'
                ),
                "steam_flow"=>array( //蒸汽流量
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>'~'
                ),
                "average_load"=>array( //平均流量
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>'~'
                ),
                "flow_total"=>array( //流量累计
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>'~'
                ),
                "hearth_pressure"=>array( //炉膛负压
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>'~'
                ),
                "give_water_temperature"=>array( //给水温度
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>'~'
                ),
                "first_wind_temperature"=>array( //一次风温
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>'~'
                ),
                "superheated_steam_temperature"=>array( //过热蒸汽气温
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>"~"
                ),
                "exit_gas_temperature"=>array( //排烟温度
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>'~'
                ),
                "hop_pocket_entry_temperature"=>array( //布袋进口温度
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>'~'
                ),
                "top_hearth_temperature"=>array( //最高炉膛温度
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>'~'
                ),
                "bottom_hearth_temperature"=>array( //最低炉膛温度
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>'~'
                )
            ),
            //炉水指标
            "boiler_water_kpi"=>array(
                "ph"=>array( //PH值
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>'~'
                ),
                "phosphoric_acid"=>array( //磷酸根
                    "no1boiler"=>0,
                    "no2boiler"=>0,
                    "no3boiler"=>0,
                    "standard"=>'~'
                )
            ),
            //其他
            "production_about"=>array(),
            //签字
            "sign" => "生技科签字 " . $sign_name . "  ".date('Y年m月d日', strtotime($date))
        );

        $date_electricity = $electricityObj->countData($date_start, $date_end, $factory, $tenement_conn);
        $month_electricity = $electricityObj->countData($month_start, $month_end, $factory, $tenement_conn);
        return $month_electricity;
    }
}
