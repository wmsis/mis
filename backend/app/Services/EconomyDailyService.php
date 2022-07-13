<?php

namespace App\Services;
use App\Http\Models\SIS\DailyData;
use App\Http\Models\SIS\EconomyDaily;

class EconomyDailyService{
    public function daydata($time)
    {
        $economyDailyObj = new EconomyDaily();
        $params = [];
        $params['time'] = $time;
        $params['period'] = 'date';

        //数据初始化
        $input_val = array();   //手动输入的每日数据
        $factory_use_electricity = 0; //厂用电量
        $factory_use_electricity_total = 0; //厂用电量累计
        $out_buy_electricity = 0; //外购电量
        $out_buy_electricity_total = 0; //外购电量累计
        $no1_electric_energy = 0; //1#机发电量
        $no1_electric_energy_total = 0; //1#机发电量累计
        $no2_electric_energy = 0; //2#机发电量
        $no2_electric_energy_total = 0; //2#机发电量累计
        $no1_online_electric_energy = 0; //1#机上网电量
        $no1_online_electric_energy_total = 0; //1#机上网电量累计
        $no2_online_electric_energy = 0; //2#机上网电量
        $no2_online_electric_energy_total = 0; //2#机上网电量累计
        $rubbish_incineration = 0; //垃圾焚烧量
        $rubbish_incineration_total = 0; //累计垃圾焚烧量
        $boiler_use_water = 0;     //锅炉用水
        $boiler_use_water_total = 0;     //锅炉累计用水

        $use_oil = 0;           //燃油耗量
        $use_oil_total = 0;     //累计燃油耗量
        $use_cement = 0;        //水泥用量
        $use_cement_total = 0;  //累计水泥用量

        $no1boiler_steam_flow = 0; //1#炉蒸汽流量
        $no2boiler_steam_flow = 0; //2#炉蒸汽流量
        $no3boiler_steam_flow = 0; //3#炉蒸汽流量
        $no1boiler_steam_flow_total = 0; //1#炉蒸汽累计流量
        $no2boiler_steam_flow_total = 0; //2#炉蒸汽累计流量
        $no3boiler_steam_flow_total = 0; //3#炉蒸汽累计流量

        //获取人工输入数据
        $inputList = $economyDailyObj->findByParams($params)->toArray();
        if(!$inputList || empty($inputList)){
            $taglist = config('economydaily.taglist');
            foreach ($taglist as $key => $value) {
                $input_val[$value['en_name']] = array(
                    "en_name"=>$value['en_name'],
                    "cn_name"=>$value['cn_name'],
                    "value"=>0,
                    "time"=>$time
                );
            }
        }
        else{
            foreach ($inputList as $key => $value) {
                unset($inputList[$key]['created_at']);
                unset($inputList[$key]['updated_at']);
                unset($inputList[$key]['deleted_at']);
                unset($inputList[$key]['period']);

                $input_val[$value['en_name']] = $value;
                if($value['en_name'] == 'factory_use_electricity'){
                    $factory_use_electricity = $value['value'];
                }
                elseif($value['en_name'] == 'out_buy_electricity'){
                    $out_buy_electricity = $value['value'];
                }
                elseif($value['en_name'] == 'no1_electric_energy'){
                    $no1_electric_energy = $value['value'];
                }
                elseif($value['en_name'] == 'no2_electric_energy'){
                    $no2_electric_energy = $value['value'];
                }
                elseif($value['en_name'] == 'no1_online_electric_energy'){
                    $no1_online_electric_energy = $value['value'];
                }
                elseif($value['en_name'] == 'no2_online_electric_energy'){
                    $no2_online_electric_energy = $value['value'];
                }
                elseif($value['en_name'] == 'rubbish_incineration'){
                    $rubbish_incineration = $value['value'];
                }
                elseif($value['en_name'] == 'boiler_use_water'){
                    $boiler_use_water = $value['value'];
                }
                elseif($value['en_name'] == 'use_oil'){
                    $use_oil = $value['value'];
                }
                elseif($value['en_name'] == 'use_cement'){
                    $use_cement = $value['value'];
                }
            }
        }

        //获取日常运行或累计数据
        $dataCompute = DailyData::where('date', $time)->get()->toArray();
        $dataComputeTemp = array();
        foreach ($dataCompute as $key => $value) {
            unset($dataCompute[$key]['_id']);
            unset($dataCompute[$key]['created_at']);
            unset($dataCompute[$key]['updated_at']);
            $dataComputeTemp[$value['en_name']] = $dataCompute[$key];

            if($value['value'] < 10){
                $dataComputeTemp[$value['en_name']]['value'] = round($value['value'], 2);
            }

            if(strpos($value['en_name'], 'run_status') !== false){
                $dataComputeTemp[$value['en_name']]['value'] = round($value['value']/3600, 2);
            }

            if($value['en_name'] == 'no1boiler@steam_flow'){
                $no1boiler_steam_flow = round($value['value'], 2);
            }
            elseif($value['en_name'] == 'no2boiler@steam_flow'){
                $no2boiler_steam_flow = round($value['value'], 2);
            }
            elseif($value['en_name'] == 'no3boiler@steam_flow'){
                $no3boiler_steam_flow = round($value['value'], 2);
            }
            elseif($value['en_name'] == 'no1boiler@steam_flow@total'){
                $no1boiler_steam_flow_total = round($value['value'], 0);
            }
            elseif($value['en_name'] == 'no2boiler@steam_flow@total'){
                $no2boiler_steam_flow_total = round($value['value'], 0);
            }
            elseif($value['en_name'] == 'no3boiler@steam_flow@total'){
                $no3boiler_steam_flow_total = round($value['value'], 0);
            }
            elseif($value['en_name'] == 'boiler_use_water@total'){
                $boiler_use_water_total = $value['value'];
            }
            elseif($value['en_name'] == 'rubbish_incineration@total'){
                $rubbish_incineration_total = $value['value'];
            }
            elseif($value['en_name'] == 'no1_electric_energy@total'){
                $no1_electric_energy_total = $value['value'];
            }
            elseif($value['en_name'] == 'no2_electric_energy@total'){
                $no2_electric_energy_total = $value['value'];
            }
            elseif($value['en_name'] == 'no1_online_electric_energy@total'){
                $no1_online_electric_energy_total = $value['value'];
            }
            elseif($value['en_name'] == 'no2_online_electric_energy@total'){
                $no2_online_electric_energy_total = $value['value'];
            }
            elseif($value['en_name'] == 'factory_use_electricity@total'){
                $factory_use_electricity_total = $value['value'];
            }
            elseif($value['en_name'] == 'out_buy_electricity@total'){
                $out_buy_electricity_total = $value['value'];
            }
            elseif($value['en_name'] == 'use_oil@total'){
                $use_oil_total = $value['value'];
            }
            elseif($value['en_name'] == 'use_cement@total'){
                $use_cement_total = $value['value'];
            }
        }

        //需要通过公式计算的值
        $func_val = array();
        $factory_use_electricity_rate = ($no1_electric_energy + $no2_electric_energy) > 0 ? round(100 * ($factory_use_electricity + $out_buy_electricity)/($no1_electric_energy + $no2_electric_energy), 2) : 0;
        $factory_use_electricity_rate_total = ($no1_electric_energy_total + $no2_electric_energy_total) > 0 ? round((100 * $factory_use_electricity_total + $out_buy_electricity_total)/($no1_electric_energy_total + $no2_electric_energy_total), 2) : 0;
        $ton_rubbish_electricity = $rubbish_incineration ? round(($no1_electric_energy + $no2_electric_energy)/$rubbish_incineration, 2) : 0;
        $ton_rubbish_electricity_total = $rubbish_incineration_total ? round(($no1_electric_energy_total + $no2_electric_energy_total)/$rubbish_incineration_total, 2) : 0;
        $ton_rubbish_online_electricity = $rubbish_incineration ? round(($no1_online_electric_energy + $no2_online_electric_energy)/$rubbish_incineration, 2) : 0;
        $ton_rubbish_online_electricity_total = $rubbish_incineration_total ? round(($no1_online_electric_energy_total + $no2_online_electric_energy_total)/$rubbish_incineration_total, 2) : 0;
        $supply_water_rate = ($no1boiler_steam_flow + $no2boiler_steam_flow + $no3boiler_steam_flow) > 0 ? round(100 * $boiler_use_water/($no1boiler_steam_flow + $no2boiler_steam_flow + $no3boiler_steam_flow), 2) : 0;
        $supply_water_rate_total = ($no1boiler_steam_flow_total + $no2boiler_steam_flow_total + $no3boiler_steam_flow_total) > 0 ? round(100 * $boiler_use_water_total/($no1boiler_steam_flow_total + $no2boiler_steam_flow_total + $no3boiler_steam_flow_total), 2) : 0;
        $lime_use = $rubbish_incineration ? round($rubbish_incineration * 0.006, 3) : 0;
        $active_carbon = $rubbish_incineration ? round($rubbish_incineration * 0.0005, 3) : 0;
        $lime_use_total = $rubbish_incineration_total ? round($rubbish_incineration_total * 0.006, 3) : 0;
        $active_carbon_total = $rubbish_incineration_total ? round($rubbish_incineration_total * 0.0005, 3) : 0;
        $no1turbine_steam_rate = $no1_electric_energy && isset($dataComputeTemp['no1turbine@entry_steam_flow']['value']) ? round(1000*$dataComputeTemp['no1turbine@entry_steam_flow']['value']/$no1_electric_energy, 2) : 0;
        $no2turbine_steam_rate = $no2_electric_energy && isset($dataComputeTemp['no2turbine@entry_steam_flow']['value']) ? round(1000*$dataComputeTemp['no2turbine@entry_steam_flow']['value']/$no2_electric_energy, 2) : 0;

        $func_val['factory_use_electricity_rate'] = array("value"=>$factory_use_electricity_rate);
        $func_val['factory_use_electricity_rate_total'] = array("value"=>$factory_use_electricity_rate_total);
        $func_val['ton_rubbish_electricity'] = array("value"=>$ton_rubbish_electricity);
        $func_val['ton_rubbish_electricity_total'] = array("value"=>$ton_rubbish_electricity_total);
        $func_val['ton_rubbish_online_electricity'] = array("value"=>$ton_rubbish_online_electricity);
        $func_val['ton_rubbish_online_electricity_total'] = array("value"=>$ton_rubbish_online_electricity_total);
        $func_val['supply_water_rate'] = array("value"=>$supply_water_rate);
        $func_val['supply_water_rate_total'] = array("value"=>$supply_water_rate_total);
        $func_val['lime_use'] = array("value"=>$lime_use);
        $func_val['active_carbon'] = array("value"=>$active_carbon);
        $func_val['lime_use_total'] = array("value"=>$lime_use_total);
        $func_val['active_carbon_total'] = array("value"=>$active_carbon_total);
        $func_val['no1turbine_steam_rate'] = array("value"=>$no1turbine_steam_rate);
        $func_val['no2turbine_steam_rate'] = array("value"=>$no2turbine_steam_rate);

        $final = array_merge($input_val, $func_val, $dataComputeTemp);
        foreach ($final as $key => $value) {
            if(strpos($key, 'no2turbine') !== false){
                $final[$key]['value'] = '';
                $final[$key]['min'] = '';
                $final[$key]['max'] = '';
            }
            elseif(strpos($key, 'under_pressure') !== false){//炉膛负压
                $final[$key]['value'] = round($value['value'], 0);
                $final[$key]['min'] = round($value['min'], 0);
                $final[$key]['max'] = round($value['max'], 0);
            }
            elseif(strpos($key, 'turbine@load') !== false){//汽机负荷
                $final[$key]['value'] = round($value['value'], 0);
                $final[$key]['min'] = round($value['min'], 0);
                $final[$key]['max'] = round($value['max'], 0);
            }
            elseif(strpos($key, 'entry_steam_pressure') !== false){//进汽压力
                $final[$key]['value'] = round($value['value'], 2);
                $final[$key]['min'] = round($value['min'], 2);
                $final[$key]['max'] = round($value['max'], 2);
            }
            elseif(strpos($key, 'entry_steam_temperature') !== false){//进汽温度
                $final[$key]['value'] = round($value['value'], 0);
                $final[$key]['min'] = round($value['min'], 0);
                $final[$key]['max'] = round($value['max'], 0);
            }
            elseif(strpos($key, 'out_steam_temperature') !== false){//排汽温度
                $final[$key]['value'] = round($value['value'], 0);
                $final[$key]['min'] = round($value['min'], 0);
                $final[$key]['max'] = round($value['max'], 0);
            }
            elseif(strpos($key, 'entry_steam_flow') !== false){//进汽流量
                $final[$key]['value'] = round($value['value'], 0);
            }
            elseif(strpos($key, 'steam_flow@total') !== false){ //累计蒸汽流量
                $final[$key]['value'] = round($value['value'], 0);
            }
            elseif(strpos($key, 'steam_flow') !== false){ //蒸汽流量
                $final[$key]['value'] = round($value['value'], 2);
            }
            elseif(strpos($key, 'steam_load') !== false){//平均负荷
                $final[$key]['value'] = round($value['value'], 2);
                $final[$key]['min'] = round($value['min'], 2);
                $final[$key]['max'] = round($value['max'], 2);
            }
            elseif(strpos($key, 'run_') !== false){
                $final[$key]['value'] = round($value['value'], 1);
            }
            elseif(strpos($key, 'steam_rate') !== false){//汽耗率
                $final[$key]['value'] = round($value['value'], 3);
            }
            elseif(strpos($key, 'vacuum') !== false){//真空度
                $final[$key]['value'] = round($value['value'], 3);
                $final[$key]['min'] = round($value['min'], 3);
                $final[$key]['max'] = round($value['max'], 3);
            }
            elseif(strpos($key, 'give_water_temperatue') !== false){//给水温度
                $final[$key]['value'] = round($value['value'], 0);
                $final[$key]['min'] = round($value['min'], 0);
                $final[$key]['max'] = round($value['max'], 0);
            }
            elseif(strpos($key, 'first_wind_temperature') !== false){//一次风温
                $final[$key]['value'] = round($value['value'], 0);
                $final[$key]['min'] = round($value['min'], 0);
                $final[$key]['max'] = round($value['max'], 0);
            }
            elseif(strpos($key, 'hot_steam_temperature') !== false){//过热蒸汽气温
                $final[$key]['value'] = round($value['value'], 0);
                $final[$key]['min'] = round($value['min'], 0);
                $final[$key]['max'] = round($value['max'], 0);
            }
            elseif(strpos($key, 'out_gas_temperature') !== false){//排烟温度
                $final[$key]['value'] = round($value['value'], 0);
                $final[$key]['min'] = round($value['min'], 0);
                $final[$key]['max'] = round($value['max'], 0);
            }
            elseif(strpos($key, 'inner_temperature') !== false){//炉膛温度
                $final[$key]['value'] = round($value['value'], 0);
                $final[$key]['min'] = round($value['min'], 0);
                $final[$key]['max'] = round($value['max'], 0);
            }
            elseif(strpos($key, 'pocket_entry_gas_temperature') !== false){//布袋进口烟温
                $final[$key]['value'] = round($value['value'], 0);
                $final[$key]['min'] = round($value['min'], 0);
                $final[$key]['max'] = round($value['max'], 0);
            }
            elseif(strpos($key, 'ph_value') !== false){
                #$final[$key]['value'] = round($value['value'], 2);
            }
            elseif(strpos($key, 'phosphate_radical') !== false){
                #$final[$key]['value'] = round($value['value'], 2);
            }
            elseif(strpos($key, 'factory_use_electricity_rate') !== false){
                $final[$key]['value'] = round($value['value'], 2);
            }
            elseif(strpos($key, 'ton_rubbish_electricity') !== false){
                $final[$key]['value'] = round($value['value'], 2);
            }
            elseif(strpos($key, 'ton_rubbish_online_electricity') !== false){
                $final[$key]['value'] = round($value['value'], 2);
            }
            elseif(strpos($key, 'supply_water_rate') !== false){
                $final[$key]['value'] = round($value['value'], 2);
            }
            elseif(strpos($key, 'use_oil') !== false){
                $final[$key]['value'] = round($value['value'], 2);
            }
            elseif(strpos($key, 'lime_use') !== false){
                $final[$key]['value'] = round($value['value'], 2);
            }
            elseif(strpos($key, 'active_carbon') !== false){
                $final[$key]['value'] = round($value['value'], 2);
            }
            elseif(strpos($key, 'use_cement') !== false){
                $final[$key]['value'] = round($value['value'], 2);
            }
            elseif(strpos($key, 'rubbish_incineration') !== false){
                $final[$key]['value'] = round($value['value'], 2);
            }
            elseif(strpos($key, 'life_rubbish_entry') !== false){
                $final[$key]['value'] = round($value['value'], 2);
            }
            else{
                $final[$key]['value'] = round($value['value']);
            }
        }
        return $final;
    }
}
