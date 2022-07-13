<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ExcelImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collections)
    {
        //如果需要去除表头
        unset($collections[0]);
        //$rows 是数组格式
        $this->createData($collections);
    }

    //手动控制存储过程
    private function createData($collections){
        //todo
    }
}
