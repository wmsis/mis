<?php

namespace App\Exports;
use Faker\Factory;
use App\Exports\BaseExport;

use Maatwebsite\Excel\Concerns\FromCollection;

class ExcelExport extends BaseExport implements FromCollection
{
    // 要导出的数据
    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
        parent::__construct('猫小鱼', '运行日报表');
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];
        if (!empty($this->data)) {
            foreach ($this->data as $key => $vo) {
               // 对应的业务和数据处理，此处省略
               // ...
               // 对于长数字字符串导出excel会变成科学计数，请在字符串前面加上 "\t"，例如：$str = "\t" . $str;
               foreach ($vo as $k => $v) {
                   $k == 'creditCardNumber' ? ($vo[$k] = "\t" . $v) : $v;
               }
               $data[] = $vo;
            }
        }

        // 此处数据需要数组转集合
        return collect($data);
    }
}
