<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class BaseExport implements WithEvents, WithStrictNullComparison, WithProperties
{
    private $author;
    private $sheetname;

    public function __construct($author='猫小鱼', $sheetname='默认')
    {
        $this->sheetname = $sheetname;
        $this->author  = $author;
    }

    public function properties(): array
    {
        return [
            'creator' => $this->author   //设置作者
        ];
    }

    /**
     * 注册事件
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setTitle('31');
                //设置区域单元格垂直居中
                $event->sheet->getDelegate()->getStyle('A1:Z1265')->getAlignment()->setVertical('center');
                //设置区域单元格水平居中
                $event->sheet->getDelegate()->getStyle('A1:Z1265')->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('A2:Q2')->getAlignment()->setHorizontal('right');

                //行高和样式
                $event->sheet->getDelegate()->getStyle('A3:Q29')->applyFromArray([
                    'borders' => array(
                        'outline' => array(
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => array('argb' => '33333300'),
                        ),
                        'allBorders' => array(
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => array('argb' => '33333300'),
                        )
                    ),
                    'font' => [
                        'name' => 'Arial',
                        'italic' => false,
                        'strikethrough' => false,
                        'size' => 9,
                        'color' => [
                            'rgb' => '333333'
                        ],
                    ]
                ]);
                $event->sheet->getDelegate()->getStyle('A1:Q1')->applyFromArray([
                    'font' => [
                        'size' => 16,
                        'bold' => true
                    ]
                ]);

                for($i =1; $i<=29; $i++){
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(30);
                }
                //$event->sheet->getDelegate()->getRowDimension(30)->setRowHeight(210);
                //$event->sheet->getDelegate()->getRowDimension(31)->setRowHeight(30);

                //合并单元格
                $event->sheet->getDelegate()->mergeCells('A1:Q1');
                $event->sheet->getDelegate()->mergeCells('A2:Q2');
                $event->sheet->getDelegate()->mergeCells('A3:D3');
                $event->sheet->getDelegate()->mergeCells('A4:A29');
                $event->sheet->getDelegate()->mergeCells('B4:B14');
                $event->sheet->getDelegate()->mergeCells('B15:B23');
                $event->sheet->getDelegate()->mergeCells('B24:B25');
                $event->sheet->getDelegate()->mergeCells('B26:B29');
                $event->sheet->getDelegate()->mergeCells('C4:C6');
                for($i =7; $i<=29; $i++){
                    $str = 'C'.$i.':'.'D'.$i;
                    $event->sheet->getDelegate()->mergeCells($str);
                }
                //$event->sheet->getDelegate()->mergeCells('A29:G30');
                //$event->sheet->getDelegate()->mergeCells('A31:G31');

                $event->sheet->getDelegate()->mergeCells('H3:I3');
                $event->sheet->getDelegate()->mergeCells('H4:H14');
                $event->sheet->getDelegate()->mergeCells('H15:H27');
                $event->sheet->getDelegate()->mergeCells('H28:H29');

                for($i =3; $i<=14; $i++){
                    $str1 = 'K'.$i.':'.'M'.$i;
                    $event->sheet->getDelegate()->mergeCells($str1);

                    $str2 = 'N'.$i.':'.'P'.$i;
                    $event->sheet->getDelegate()->mergeCells($str2);
                }

                for($i =15; $i<=29; $i++){
                    $str1 = 'K'.$i.':'.'L'.$i;
                    $event->sheet->getDelegate()->mergeCells($str1);

                    $str2 = 'M'.$i.':'.'N'.$i;
                    $event->sheet->getDelegate()->mergeCells($str2);

                    $str3 = 'O'.$i.':'.'P'.$i;
                    $event->sheet->getDelegate()->mergeCells($str3);
                }

                // $rows = ['I', 'J', 'Q'];
                // foreach ($rows as $key) {
                //     $str1 = $key.'28'.':'.$key.'29';
                //     $event->sheet->getDelegate()->mergeCells($str1);
                //
                //     $str2 = $key.'30'.':'.$key.'31';
                //     $event->sheet->getDelegate()->mergeCells($str2);
                // }

                // $str1 = 'K28'.':'.'L29';
                // $event->sheet->getDelegate()->mergeCells($str1);
                //
                // $str2 = 'M28'.':'.'N29';
                // $event->sheet->getDelegate()->mergeCells($str2);
                //
                // $str3 = 'O28'.':'.'P29';
                // $event->sheet->getDelegate()->mergeCells($str3);
                //
                // $str4 = 'K30'.':'.'L31';
                // $event->sheet->getDelegate()->mergeCells($str4);
                //
                // $str5 = 'M30'.':'.'N31';
                // $event->sheet->getDelegate()->mergeCells($str5);
                //
                // $str6 = 'O30'.':'.'P31';
                // $event->sheet->getDelegate()->mergeCells($str6);

                //换行
                $event->sheet->getDelegate()->getStyle('A4:A29')->getAlignment()->setWrapText(TRUE);
                $event->sheet->getDelegate()->getStyle('H4:H14')->getAlignment()->setWrapText(TRUE);
                $event->sheet->getDelegate()->getStyle('H5:H27')->getAlignment()->setWrapText(TRUE);
                $event->sheet->getDelegate()->getStyle('H28:H29')->getAlignment()->setWrapText(TRUE);
                $event->sheet->getDelegate()->getStyle('B4:B14')->getAlignment()->setWrapText(TRUE);
                $event->sheet->getDelegate()->getStyle('B15:B23')->getAlignment()->setWrapText(TRUE);
                $event->sheet->getDelegate()->getStyle('B24:B25')->getAlignment()->setWrapText(TRUE);
                $event->sheet->getDelegate()->getStyle('B26:B29')->getAlignment()->setWrapText(TRUE);
                $event->sheet->getDelegate()->getStyle('C4:C6')->getAlignment()->setWrapText(TRUE);
                $event->sheet->getDelegate()->getStyle('C8:D28')->getAlignment()->setWrapText(TRUE);
                $event->sheet->getDelegate()->getStyle('I4:I27')->getAlignment()->setWrapText(TRUE);
                //$event->sheet->getDelegate()->getStyle('A29:G30')->getAlignment()->setWrapText(TRUE);

                //设置单元格宽度
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

                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth($width);
                }

                if(!empty($this->sheetname)){
                    $event->sheet->getDelegate()->setTitle($this->sheetname);
                }
            }
        ];
    }
}
