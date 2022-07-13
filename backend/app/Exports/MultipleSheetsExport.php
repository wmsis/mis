<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheetsExport implements WithMultipleSheets
{
    use Exportable;

    protected $datalist;

    public function __construct($datalist)
    {
        $this->datalist = $datalist;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->datalist as $key => $data) {
            //不同的数据可以调用不同的方法
            $sheets[] = new SheetContentExport($key, $data);
        }

        return $sheets;
    }
}
