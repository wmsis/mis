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

class ExportEconomyDailyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $date;

    /**
     * Create a new job instance.
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
        $datalist = EconomyDailyService::daydata($this->date);
        $titles = ['温州龙湾伟明环保能源有限公司技术经济指标日报表'];
        $date = [date('Y年m月d日', strtotime($this->date))];
        $headings = ['项目', '', '', '', '单位', '本日', '本月累计', '项目', '', '单位', '1#机', '', '', '2#机',  '', '', '标准值'];
        $final_data = $this->template($datalist);
        array_unshift($final_data, $titles, $date, $headings);

        $excel = new BaseExport($final_data, $author='猫小鱼', $sheetname='经济日报表');

        //合并单元格
        $merge_cell_arr = array();
        $merge_cell_arr[] = 'A2:B3';
        $merge_cell_arr[] = 'A12:A14';
        $merge_cell_arr[] = 'A15:A17';
        $merge_cell_arr[] = 'A18:A20';
        $merge_cell_arr[] = 'A21:A24';
        $merge_cell_arr[] = 'A25:A28';
        for($i=4; $i<=11; $i++){
            $merge_cell_arr[] = 'A'.$i.':B'.$i;
        }
        for($i=2; $i<=24; $i++){
            $merge_cell_arr[] = 'C'.$i.':D'.$i;
            $merge_cell_arr[] = 'E'.$i.':F'.$i;
            $merge_cell_arr[] = 'G'.$i.':H'.$i;
        }
        //$excel->setMergeCells($merge_cell_arr);

        $columnWidth = array('A'=>15, 'B'=>20);
        //$excel->setColumnWidth($columnWidth);

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
            'fcell' => '',
            'gcell' => '',
            'hcell' => '汽机运行指标',
            'icell' => '运行时间',
            'jcell' => '小时',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow1;
        $dataRow2 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '',
            'dcell' => '2#机',
            'ecell' => 'kwh',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '运行时间累计',
            'jcell' => '小时',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow2;
        $dataRow3 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '',
            'dcell' => '合计',
            'ecell' => 'kwh',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '最高负荷',
            'jcell' => 'kw',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow3;
        $dataRow4 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '厂用电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '平均负荷',
            'jcell' => 'kw',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow4;
        $dataRow5 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '1#机上网电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '进汽压力',
            'jcell' => 'MPa',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow5;
        $dataRow6 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '2#机上网电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '进汽温度',
            'jcell' => '℃',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow6;
        $dataRow7 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '总计上网电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '排汽温度',
            'jcell' => '℃',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow7;
        $dataRow8 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '厂用电率',
            'dcell' => '',
            'ecell' => '%',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '进汽流量',
            'jcell' => '吨',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow8;
        $dataRow9 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '外购电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '进汽流量累计',
            'jcell' => '吨',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow9;
        $dataRow10 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '渗沥水处理站用电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '汽耗率',
            'jcell' => 'kg/度',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow10;
        $dataRow11 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '一期供电',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '真空度',
            'jcell' => 'MPa',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow11;
        $dataRow12 = [
            'acell' => '',
            'bcell' => '消耗指标',
            'ccell' => '吨垃圾发电量',
            'dcell' => '',
            'ecell' => 'kwh',
            'fcell' => '',
            'gcell' => '',
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
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '运行时间',
            'jcell' => '小时',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow13;
        $dataRow14 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '外购水量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '蒸汽流量',
            'jcell' => '吨',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow14;
        $dataRow15 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '其中锅炉用水量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '平均流量',
            'jcell' => '吨',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow15;
        $dataRow16 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '补水率',
            'dcell' => '',
            'ecell' => '%',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '流量累计',
            'jcell' => '吨',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow16;
        $dataRow17 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '燃油耗量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '炉膛负压',
            'jcell' => 'Pa-Pa',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow17;
        $dataRow18 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '石灰耗量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '给水温度',
            'jcell' => '℃-℃',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow18;
        $dataRow19 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '水泥耗量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '一次风温',
            'jcell' => '℃-℃',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow19;
        $dataRow20 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '活性炭耗量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '过热蒸汽风温',
            'jcell' => '℃-℃',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow20;
        $dataRow21 = [
            'acell' => '',
            'bcell' => '燃烧指标',
            'ccell' => '垃圾进库量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '排烟温度',
            'jcell' => '℃-℃',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow21;
        $dataRow22 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '垃圾焚烧量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '布袋进口烟温',
            'jcell' => '℃-℃',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow22;
        $dataRow23 = [
            'acell' => '',
            'bcell' => '污水指标',
            'ccell' => '污水处理量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '最高炉膛温度',
            'jcell' => '℃',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow23;
        $dataRow24 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '污水站污水处理量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '最低炉膛温度',
            'jcell' => '℃',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow24;
        $dataRow25 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '污水站污水出水量',
            'dcell' => '',
            'ecell' => '吨',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '炉水指标',
            'icell' => 'PH值',
            'jcell' => '/',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow25;
        $dataRow26 = [
            'acell' => '',
            'bcell' => '',
            'ccell' => '污水出水COD指标',
            'dcell' => '',
            'ecell' => 'mg/L',
            'fcell' => '',
            'gcell' => '',
            'hcell' => '',
            'icell' => '磷酸根',
            'jcell' => 'mg/L',
            'kcell' => '',
            'lcell' => '',
            'mcell' => '',
            'ncell' => '',
            'ocell' => '',
            'pcell' => '',
            'qcell' => '',
        ];
        $result[] = $dataRow26;

        return $result;
    }
}
