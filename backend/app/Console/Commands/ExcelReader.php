<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Settings;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class ExcelReader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excel:reader {path} {--drive=laravel-excel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'excel reader';

    /**
     * 测试文件的行数
     * @var int
     */
    private $rows = 0;

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
        $path = $this->argument('path');
        $option = $this->option('drive');

        switch ($option) {
            case 'laravel-excel':
                $this->useLaravelExcelDrive($path);
                break;
            case 'spout':
                $this->useSpoutDrive($path);
                break;
            default:
                throw new \Exception('Invalid option ' . $option);
        }

        echo (sprintf('共读取数据：%s 行', $this->rows));
        echo (sprintf('共耗时：%s秒', $this->timeUsage));
        echo (sprintf('共消耗内存: %s', $this->memoryUsage));
    }

    //php artisan excel:reader public/spoutWriter.xlsx --drive=spout读取storage app 里面的文件
    private function useSpoutDrive($path){
        ini_set('memory_limit', -1);
        $start = now();
        $reader = ReaderEntityFactory::createXLSXReader();
        //$reader->setShouldFormatDates(true);
        $reader->open(storage_path('app/'.$path));
        $rows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                //$cells = $row->getCells();
                $this->rows++;
                $rows[] = $row;
            }
        }
        $this->timeUsage = now()->diffInSeconds($start);
        $this->memoryUsage = $this->convert(memory_get_usage());
        $reader->close();
    }

    //php artisan excel:reader public/excel/laravelWriter.xlsx 读取public里的文件
    private function useLaravelExcelDrive($path)
    {
        $start = now();
        // 这段代码是用来处理导入时报的一个读取XML过大的错误，与本文无关不过多赘述
        Settings::setLibXmlLoaderOptions(LIBXML_COMPACT | LIBXML_PARSEHUGE);
        ini_set('memory_limit', -1);
        $array = Excel::toArray(new ExcelImport(), $path);
        $this->rows = count($array[0]);
        $this->timeUsage = now()->diffInSeconds($start);
        $this->memoryUsage = $this->convert(memory_get_usage());
    }

    private function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
}
