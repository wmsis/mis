<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\ExcelExport;
use App\Exports\ParentExport;
use App\Exports\MultipleSheetsExport;
use Maatwebsite\Excel\Facades\Excel;
use Faker\Factory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use App\Http\Models\SIS\EconomyDaily;
use App\Http\Models\SIS\DailyData;

class ExcelWriter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excel:writer {--drive=laravel-excel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'excel writer';

    /**
     * 程序运行消耗的时间(s)
     * @var int
     */
    private $timeUsage = 0;

    /**
     * 程序运行消耗的内存(byte)
     * @var int
     */
    private $memoryUsage = 0;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->option('drive');

        switch ($option) {
            case 'laravel-excel':
                $this->useLaravelExcelDrive();
                break;
            case 'method':
                $this->useMethodDrive();
                break;
            case 'multi':
                $this->useMultiDrive();
                break;
            case 'spout':
                $this->useSpoutDrive();
                break;
            default:
                throw new \Exception('Invalid option ' . $option);
        }

        echo (sprintf('共耗时：%s秒', $this->timeUsage));
        echo (sprintf('共消耗内存: %s', $this->memoryUsage));
    }

    private function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    private function useLaravelExcelDrive()
    {
        ini_set('memory_limit', -1);
        $start = now();
        $data = $this->generateData();

        // download 方法直接下载，store 方法可以保存
        Excel::store(new ExcelExport($data), 'laravelWriter.xlsx');
        $this->timeUsage = now()->diffInSeconds($start);
        $this->memoryUsage = $this->convert(memory_get_usage());
    }

    private function useSpoutDrive()
    {
        ini_set('memory_limit', -1);
        $start = now();
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile(storage_path('app/spoutWriter.xlsx'));
        $data = $this->generateData();
        $result = array();
        foreach ($data as $key => $value) {
            $cells = array();
            foreach ($value as $k => $v) {
                $cells[] = WriterEntityFactory::createCell($v);
            }
            $result[] = WriterEntityFactory::createRow($cells);;
        }
        $writer->addRows($result);
        $writer->close();
        $this->timeUsage = now()->diffInSeconds($start);
        $this->memoryUsage = $this->convert(memory_get_usage());
    }

    private function useMethodDrive(){
        ini_set('memory_limit', -1);
        $start = now();
        $this->methodData();
        $this->timeUsage = now()->diffInSeconds($start);
        $this->memoryUsage = $this->convert(memory_get_usage());
    }

    private function useMultiDrive(){
        ini_set('memory_limit', -1);
        $start = now();
        $this->multiData();
        $this->timeUsage = now()->diffInSeconds($start);
        $this->memoryUsage = $this->convert(memory_get_usage());
    }

    private function generateData(){
        $faker = Factory::create('zh_CN');
        $result = [];
        for ($i = 0; $i < 10000; $i++) {
            $arr = [
                'name' => $faker->name,
                'age' =>$faker->randomNumber(),
                'email' => $faker->email,
                'address' => $faker->address,
                'company' => $faker->company,
                'country' => $faker->country,
                'birthday' => $faker->date(),
                'city' => $faker->city,
                'creditCardNumber' => $faker->creditCardNumber,
                'street' => $faker->streetName,
                'postCode' => $faker->postcode,
            ];
            $result[] = $arr;
        }

        $headings = [
            '姓名',
            '年龄',
            '邮箱',
            '地址',
            '公司',
            '国家',
            '出生日期',
            '城市',
            '身份证号码',
            '街道',
            '邮编',
        ];
        array_unshift($result, $headings);

        return $result;
    }

    private function methodData(){
        //设置表头
        $header = [[
            "id"=>'ID',
            "nickname"=>'用户昵称',
            "gender_text"=>'性别',
            "mobile"=>'手机号',
            "addtime"=>'创建时间  '
        ]];

        //数据
        $data = [
            0=>[
                "id"=>'1',
                "nickname"=>'张三',
                "gender_text"=>'男',
                "mobile"=>'18812345678',
                "addtime"=>'2019-11-21  '
            ],
            2=>[
                "id"=>'2',
                "nickname"=>'李四',
                "gender_text"=>'女',
                "mobile"=>'18812349999',
                "addtime"=>'2019-11-21  '
            ]
        ];

        //执行导出
        $excel = new ParentExport($data, $header, '导出sheetName');
        $excel->setColumnWidth(['B' => 15, 'C' => 15, 'D' => 20, 'E' => 20]);
        $excel->setRowHeight([1 => 40, 2 => 50]);
        $excel->setFont(['A1:Z1265' => '宋体']);
        $excel->setFontSize(['A1:I1' => 14,'A2:Z1265' => 10]);
        $excel->setBold(['A1:Z2' => true]);
        $excel->setBackground(['A1:A1' => '808080','C1:C1' => '708080']);
        $excel->setMergeCells(['A1:E1']);
        $excel->setBorders(['A2:E5' => '#000000']);

        // download 方法直接下载，store 方法可以保存
        Excel::store($excel, 'methodWriter.xlsx');
    }

    private function multiData(){
        $arr = [];
        for ($day = 1; $day <= 31; $day++) {
            $data = $this->statementData();
            $arr[$day.'日'] = $data;
        }
        //执行导出
        $excel = new MultipleSheetsExport($arr);

        // download 方法直接下载，store 方法可以保存
        Excel::store($excel, 'monthWriter.xlsx');
    }
}
