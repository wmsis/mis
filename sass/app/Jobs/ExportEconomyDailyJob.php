<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BaseExport;
use EconomyDailyService;
use Log;

class ExportEconomyDailyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $date;
    protected $tenement_conn; //租户连接
    protected $factory;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params=null)
    {
        $this->date = $params && isset($params['date']) ? $params['date'] : '';
        $this->tenement_conn = $params && isset($params['tenement_conn']) ? $params['tenement_conn'] : '';
        $this->factory = $params && isset($params['factory']) ? $params['factory'] : '';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', -1);
        $data = EconomyDailyService::daydata($this->date, $this->tenement_conn, $this->factory);
        $titles = [$this->factory->name . '技术经济指标日报表'];
        $date = [date('Y年m月d日', strtotime($this->date))];
        $headings = ['项目', '', '', '', '单位', '本日', '本月累计', '项目', '', '单位', '1#机', '', '', '2#机',  '', '', '标准值'];
        $final_data = $this->template($data);
        array_unshift($final_data, $titles, $date, $headings);
        $excel = new BaseExport($final_data, $author='猫小鱼', $sheetname='经济日报表');

        //合并单元格
        $merge_cell_arr = array();
        $merge_cell_arr[] = 'A1:Q1';
        $merge_cell_arr[] = 'A2:Q2';
        $merge_cell_arr[] = 'A3:D3';
        $merge_cell_arr[] = 'A4:A29';
        $merge_cell_arr[] = 'B4:B14';
        $merge_cell_arr[] = 'B15:B23';
        $merge_cell_arr[] = 'B24:B25';
        $merge_cell_arr[] = 'B26:B29';
        $merge_cell_arr[] = 'C4:C6';
        $merge_cell_arr[] = 'H3:I3';
        $merge_cell_arr[] = 'H4:H14';
        $merge_cell_arr[] = 'H15:H27';
        $merge_cell_arr[] = 'H28:H29';
        for($i =7; $i<=29; $i++){
            $str = 'C'.$i.':'.'D'.$i;
            $merge_cell_arr[] = $str;
        }
        for($i =3; $i<=14; $i++){
            $str1 = 'K'.$i.':'.'M'.$i;
            $merge_cell_arr[] = $str1;

            $str2 = 'N'.$i.':'.'P'.$i;
            $merge_cell_arr[] = $str2;
        }

        for($i =15; $i<=29; $i++){
            $str1 = 'K'.$i.':'.'L'.$i;
            $merge_cell_arr[] = $str1;

            $str2 = 'M'.$i.':'.'N'.$i;
            $merge_cell_arr[] = $str2;

            $str3 = 'O'.$i.':'.'P'.$i;
            $merge_cell_arr[] = $str3;
        }
        $excel->setMergeCells($merge_cell_arr);

        //设置单元格宽度
        $columnWidth = [];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];
        foreach ($columns as $key=>$column) {
            $width = 5;
            if($column == 'A' || $column == 'B' || $column == 'C' || $column == 'H'){
                $width = 3.5;
            }
            if($column == 'F' || $column == 'G'){
                $width = 9;
            }
            elseif($column == 'I'){
                $width = 11;
            }
            elseif($column == 'D' || $column == 'Q'){
                $width = 8;
            }
            $columnWidth[$column] = $width;
        }
        $excel->setColumnWidth($columnWidth);

        //行高
        $cell_height_arr = [];
        for($i=1; $i<=29; $i++){
            $cell_height_arr[$i] = 30;
        }
        $excel->setRowHeight($cell_height_arr);

        //换行
        $wrap_cells = array();
        $wrap_cells[] = 'A4:A29';
        $wrap_cells[] = 'H4:H14';
        $wrap_cells[] = 'H5:H27';
        $wrap_cells[] = 'H28:H29';
        $wrap_cells[] = 'B4:B14';
        $wrap_cells[] = 'B15:B23';
        $wrap_cells[] = 'B24:B25';
        $wrap_cells[] = 'B26:B29';
        $wrap_cells[] = 'C4:C6';
        $wrap_cells[] = 'C8:D28';
        $wrap_cells[] = 'I4:I27';
        $excel->setWrapText($wrap_cells);

        //居右
        $excel->setRightCells(['A2:Q2']);

        //字体大小
        $excel->setFontSize(['A2:Q29' => 9, 'A1:Q1' => 16]);
        $excel->setBold(['A1:Q1' => true]);

        //边框
        $excel->setBorders(['A1:Q29' => '#000000']);

        // download 方法直接下载，store 方法可以保存
        Excel::store($excel, 'economy_daily_' . $this->date . '.xlsx');
    }

    private function template($data){
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

        return $result;
    }
}
