<?php

namespace App\Services;
use App\Http\Models\SIS\ClassStatement;

class ClassStatementService{
    public function daydata($date, $class_name)
    {
        $duty_name = '';
        $params['date'] = $date;
        $params['class_name'] = $class_name;
        $classStatementObj = new ClassStatement();
        $datalist = $classStatementObj->findByParams($params);
        $datalist = $datalist->toArray();
        if($datalist && count($datalist) > 0){
            $duty_name = $datalist[0]['duty_name'];
        }
        else{
            $temp = array();
            $taglist = config('classstatement.taglist');
            foreach ($taglist as $key => $item) {
                $item['value'] = 0;
                $item['duty_name'] = '';
                unset($item['period']);
                $temp[] = $item;
            }

            $datalist = $temp;
        }

        //初始值
        $final = array();
        $final['duty_name'] = array(
            'tag_en_name' => 'duty_name',
            'tag_cn_name' => '值班人',
            'duty_name' => $duty_name,
            'value' => $duty_name
        );
        $online_electric_energy = 0;
        $factory_use_electricity = 0;
        $factory_use_electricity_rate = '0%';
        $no1_electric_energy = 0;
        $no2_electric_energy = 0;
        $total_electric_energy = 0;
        $no1_entry_steam = 0;
        $no2_entry_steam = 0;
        $total_entry_steam = 0;
        $no1boiler_run_time = 0;
        $no2boiler_run_time = 0;
        $no3boiler_run_time = 0;
        $no4boiler_run_time = 0;
        $no1boiler_produce_steam = 0;
        $no2boiler_produce_steam = 0;
        $no3boiler_produce_steam = 0;
        $no4boiler_produce_steam = 0;
        $no1turbine_steam_rate = '0';
        $no2turbine_steam_rate = '0';
        $average_steam_rate = '0';
        $no1boiler_produce_steam_per_hour = 0;
        $no2boiler_produce_steam_per_hour = 0;
        $no3boiler_produce_steam_per_hour = 0;
        $no4boiler_produce_steam_per_hour = 0;
        foreach ($datalist as $key => $item) {
            if(isset($datalist[$key]['created_at'])){
                unset($datalist[$key]['created_at']);
                unset($datalist[$key]['updated_at']);
                unset($datalist[$key]['deleted_at']);
                unset($datalist[$key]['date']);
            }

            $final[$item['tag_en_name']] = $item;
            if($item['tag_en_name'] == 'online_electric_energy'){
                $online_electric_energy = $item['value'];
            }
            elseif($item['tag_en_name'] == 'factory_use_electricity'){
                $factory_use_electricity = $item['value'];
            }
            elseif($item['tag_en_name'] == 'no1_electric_energy'){
                $no1_electric_energy = $item['value'];
            }
            elseif($item['tag_en_name'] == 'no2_electric_energy'){
                $no2_electric_energy = $item['value'];
            }
            elseif($item['tag_en_name'] == 'no1_entry_steam'){
                $no1_entry_steam = $item['value'];
            }
            elseif($item['tag_en_name'] == 'no2_entry_steam'){
                $no2_entry_steam = $item['value'];
            }
            elseif($item['tag_en_name'] == 'no1boiler_run_time'){
                $no1boiler_run_time = $item['value'];
            }
            elseif($item['tag_en_name'] == 'no2boiler_run_time'){
                $no2boiler_run_time = $item['value'];
            }
            elseif($item['tag_en_name'] == 'no3boiler_run_time'){
                $no3boiler_run_time = $item['value'];
            }
            elseif($item['tag_en_name'] == 'no4boiler_run_time'){
                $no4boiler_run_time = $item['value'];
            }
            elseif($item['tag_en_name'] == 'no1boiler_produce_steam'){
                $no1boiler_produce_steam = $item['value'];
            }
            elseif($item['tag_en_name'] == 'no2boiler_produce_steam'){
                $no2boiler_produce_steam = $item['value'];
            }
            elseif($item['tag_en_name'] == 'no3boiler_produce_steam'){
                $no3boiler_produce_steam = $item['value'];
            }
            elseif($item['tag_en_name'] == 'no4boiler_produce_steam'){
                $no4boiler_produce_steam = $item['value'];
            }
        }

        //计算值
        $total_electric_energy = $no1_electric_energy + $no2_electric_energy;
        $total_entry_steam = $no1_entry_steam + $no2_entry_steam;
        $no1turbine_steam_rate = $no1_electric_energy ? round(($no1_entry_steam * 1000 / $no1_electric_energy), 2) : '0';
        $no2turbine_steam_rate = $no2_electric_energy ? round(($no2_entry_steam * 1000 / $no2_electric_energy), 2) : '0';
        $average_steam_rate = ($no1_electric_energy + $no2_electric_energy) ? round((($no1_entry_steam + $no2_entry_steam) * 1000 / ($no1_electric_energy + $no2_electric_energy)), 2) : '0';
        $factory_use_electricity_rate = $online_electric_energy ? round((100 * $factory_use_electricity / $online_electric_energy), 2) . '%' : '0%';
        $no1boiler_produce_steam_per_hour = $no1boiler_run_time ? round($no1boiler_produce_steam/$no1boiler_run_time, 2) : 0;
        $no2boiler_produce_steam_per_hour = $no2boiler_run_time ? round($no2boiler_produce_steam/$no2boiler_run_time, 2) : 0;
        $no3boiler_produce_steam_per_hour = $no3boiler_run_time ? round($no3boiler_produce_steam/$no3boiler_run_time, 2) : 0;
        $no4boiler_produce_steam_per_hour = $no4boiler_run_time ? round($no4boiler_produce_steam/$no4boiler_run_time, 2) : 0;

        $final['total_electric_energy'] = array(
            'type' => 'compute',
            'tag_en_name' => 'total_electric_energy',
            'tag_cn_name' => '总发电量',
            'duty_name' => $duty_name,
            'value' => $total_electric_energy
        );
        $final['total_entry_steam'] = array(
            'type' => 'compute',
            'tag_en_name' => 'total_entry_steam',
            'tag_cn_name' => '总进汽量',
            'duty_name' => $duty_name,
            'value' => $total_entry_steam
        );
        $final['no1turbine_steam_rate'] = array(
            'type' => 'compute',
            'tag_en_name' => 'no1turbine_steam_rate',
            'tag_cn_name' => '1#汽机汽耗率',
            'duty_name' => $duty_name,
            'value' => $no1turbine_steam_rate
        );
        $final['no2turbine_steam_rate'] = array(
            'type' => 'compute',
            'tag_en_name' => 'no2turbine_steam_rate',
            'tag_cn_name' => '2#汽机汽耗率',
            'duty_name' => $duty_name,
            'value' => $no2turbine_steam_rate
        );
        $final['average_steam_rate'] = array(
            'type' => 'compute',
            'tag_en_name' => 'average_steam_rate',
            'tag_cn_name' => '平均汽耗率',
            'duty_name' => $duty_name,
            'value' => $average_steam_rate
        );
        $final['factory_use_electricity_rate'] = array(
            'type' => 'compute',
            'tag_en_name' => 'factory_use_electricity_rate',
            'tag_cn_name' => '厂用电率',
            'duty_name' => $duty_name,
            'value' => $factory_use_electricity_rate
        );
        $final['no1boiler_produce_steam_per_hour'] = array(
            'type' => 'compute',
            'tag_en_name' => 'no1boiler_produce_steam_per_hour',
            'tag_cn_name' => '1#炉每小时产汽量',
            'duty_name' => $duty_name,
            'value' => $no1boiler_produce_steam_per_hour
        );
        $final['no2boiler_produce_steam_per_hour'] = array(
            'type' => 'compute',
            'tag_en_name' => 'no2boiler_produce_steam_per_hour',
            'tag_cn_name' => '2#炉每小时产汽量',
            'duty_name' => $duty_name,
            'value' => $no2boiler_produce_steam_per_hour
        );
        $final['no3boiler_produce_steam_per_hour'] = array(
            'type' => 'compute',
            'tag_en_name' => 'no3boiler_produce_steam_per_hour',
            'tag_cn_name' => '3#炉每小时产汽量',
            'duty_name' => $duty_name,
            'value' => $no3boiler_produce_steam_per_hour
        );
        $final['no4boiler_produce_steam_per_hour'] = array(
            'type' => 'compute',
            'tag_en_name' => 'no4boiler_produce_steam_per_hour',
            'tag_cn_name' => '4#炉每小时产汽量',
            'duty_name' => $duty_name,
            'value' => $no4boiler_produce_steam_per_hour
        );

        return $final;
    }
}
