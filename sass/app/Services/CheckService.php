<?php

namespace App\Services;
use App\Models\MIS\CheckPointDetail;
use App\Models\MIS\CheckTag;
use App\Models\MIS\CheckTagDetail;
use App\Models\MIS\ClassSchdule;
use App\Models\SIS\Orgnization;
use App\Models\SIS\Electricity;
use App\Models\SIS\ElectricityMap;
use App\Models\SIS\DcsStandard;
use Illuminate\Support\Facades\DB;
use Log;

class CheckService{
    /*
    * @return 返回用户班次内的的上网电量信息
    */
    public function userClassPoint($date)
    {
        $final = [];
        $date_users_schdule = ClassSchdule::where('date', $date)->get();
        //循环遍历每人的排班计划，计算其上班期间的发电量
        foreach ($date_users_schdule as $key => $user_schdule) {
            $start = $date . ' ' . $user_schdule->start . ':00'; //上班开始时间
            $end = $date . ' ' . $user_schdule->end . ':00'; //上班结束时间
            $orgnization = Orgnization::find($user_schdule->orgnization_id);
            if($orgnization){
                $rangedata = $this->getRangeElectricity($orgnization, $start, $end);//获取发电量原始信息
                $powerdata = $this->getRangePower($rangedata);//计算上网电量信息
                $final[$user_schdule->user_id] = [
                    'value' => $powerdata,
                    'orgnization_id' => $user_schdule->orgnization_id
                ];
            }
        }
        
        foreach ($final as $user_id => $item) {
            $where = [
                'orgnization_id'=>$item['orgnization_id'],
                'user_id'=>$user_id,
                'date'=>$date
            ];
            $values = [
                'value'=>$item['value'],
                'reason'=> '排班周期内的上网电量',
                'type'=> 'class'
            ];

            CheckPointDetail::updateOrCreate($where, $values);
        }
    }

    //获取时间段内组织的数据  正向有功总电量类似这种值
    private function getRangeElectricity($orgnization, $start, $end){
        $final = [];
        $maps = ElectricityMap::where('orgnization_id', $orgnization->id)->get();
        foreach ($maps as $k9 => $item) {
            $final[$item->id] = 0; //初始化
        }

        $electricity_table = 'electricity_' . $orgnization->code;
        $electricity_obj = (new Electricity())->setTable($electricity_table);  //乘完倍率和系数之后的数据

        //区间段内最大值
        $electricity_max = $electricity_obj->where('created_at', '>', $start)
            ->where('created_at', '<', $end)
            ->selectRaw('MAX(actual_value) as val, electricity_map_id')
            ->groupBy('electricity_map_id')
            ->get();

        //区间段内最小值
        $electricity_min = $electricity_obj->where('created_at', '>', $start)
            ->where('created_at', '<', $end)
            ->selectRaw('MIN(actual_value) as val, electricity_map_id')
            ->groupBy('electricity_map_id')
            ->get();

        //时间段内最大值格式化
        $max_key_val = array();
        foreach ($electricity_max as $key => $item) {
            $max_key_val[$item->electricity_map_id] = $item->val;
        }

        //时间段内最小值格式化
        $min_key_val = array();
        foreach ($electricity_min as $key => $item) {
            $min_key_val[$item->electricity_map_id] = $item->val;
        }

        //保存累计值
        foreach ($max_key_val as $key => $val) {
            if(isset($min_key_val[$key])){
                $value = $max_key_val[$key] - $min_key_val[$key];
                $final[$key] = $value;
            }
        }

        return $final;
    }

    //计算上网电量的值 以永强二期为例  为3#发电机电度表正向有功总电量 + 4#发电机电度表正向有功总电量
    private function getRangePower($datalist){
        $val = 0;
        $standard_cfg = config('standard.not_dcs.swdl');
        $dcs_standard = DcsStandard::where('en_name', $standard_cfg['en_name'])->first();
        if($dcs_standard){
            $row = DB::table('dcs_standard')
                ->join('power_map', 'power_map.dcs_standard_id', '=', 'dcs_standard.id')
                ->select('power_map.*', 'dcs_standard.cn_name', 'dcs_standard.en_name')
                ->where('dcs_standard.id', $dcs_standard->id)
                ->whereNull('dcs_standard.deleted_at')
                ->whereNull('power_map.deleted_at')
                ->first();

            $id_arr = explode(',', $row->electricity_map_ids);
            $func = $row->func;

            //地址键值对
            $key_values = [];
            //初始化
            foreach ($id_arr as $id) {
                $map = ElectricityMap::find($id);
                $key_values[$map->cn_name] = 0;
            }
            //赋值
            foreach ($datalist as $electricity_map_id => $data) {
                $map = ElectricityMap::find($electricity_map_id);
                $key_values[$map->cn_name] = $data;
            }

            if($func && count($key_values) > 0){
                //计算函数的值
                foreach ($key_values as $key => $value) {
                    $func = str_replace('[' . $key . ']', $value, $func);
                }

                $val = eval("return $func;");
            }

            if(strpos($val, '.') !== false){
                $val = number_format($val, 2);
            }
        }

        return $val;
    }
}
