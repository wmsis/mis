<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Exports\BaseExport;

class SheetContentExport extends BaseExport implements FromCollection
{
    private $sheetname;
    private $data;

    public function __construct($sheetname, $data)
    {
        $this->sheetname = $sheetname;
        $this->data  = $data;
        parent::__construct('猫小鱼', $this->sheetname);
    }

    public function collection()
    {
        return collect($this->data);
    }
}
