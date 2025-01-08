<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\Factory\Mssql\AvsData AS FactoryAvsData;       //电厂数据模型（永强二期品牌）
use App\Models\Factory\Mssql\WeighData AS FactoryWeighData;   //电厂数据模型(托利多品牌)
use App\Models\SIS\WeighBridge;                                //本地数据模型
use App\Models\SIS\WeighBridgeFormat;
use App\Models\SIS\WeighbridgeCateSmall;
use Illuminate\Support\Facades\Artisan;
use ErrorException;
use Illuminate\Support\Facades\Log;

/**
 * 从电厂获取地磅数据并保存到本地
 */
class AvsDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $date;
    protected $tenement_conn;
    protected $remote_conn;
    protected $local_table;
    protected $avs_type;
    protected $local_format_table;
    public $tries = 3;

    /**
     * Create a new job instance.
     * @param date 获取数据的日期
     * @param connection 获取数据的远程数据库连接
     * @param table 存储数据的本地数据库表
     * @param avs_type 地磅品牌
     * @param local_format_table 本地格式化数据库表
     * @return void
     */
    public function __construct($date=null, $tenement_conn=null, $remote_conn=null, $local_table=null, $avs_type=null, $local_format_table=null)
    {
        Log::info("333333333333");
        $this->date = $date;
        $this->tenement_conn = $tenement_conn;
        $this->remote_conn = $remote_conn;
        $this->local_table = $local_table;
        $this->avs_type = $avs_type;
        $this->local_format_table = $local_format_table;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("44444444444444444");
        try{
            $factoryAvsData = (new FactoryAvsData())->setConnection($this->remote_conn);  //连接电厂内部数据库（永强二期品牌地磅）
            $factoryWeighData = (new FactoryWeighData())->setConnection($this->remote_conn);  //连接电厂内部数据库（托利多）
            $localAvsData = (new WeighBridge())->setConnection($this->tenement_conn)->setTable($this->local_table); //连接特定租户下面的本地对应电厂数据库表
        
            $latest_row = $localAvsData->findLatestReport();
            if($latest_row){
                $timestamp = strtotime($latest_row->taredatetime);
            }
            else{
                $timestamp = time() - 24 * 60 * 60;
            }

            if($this->avs_type == 'toledo'){
                Log::info("555555555");
                $rows = $factoryWeighData->select(['*'])
                ->where('taredatetime', '>=',date("Y-m-d H:i:s", $timestamp))
                ->whereNotNull("net")
                ->orderBy("taredatetime", "ASC")
                ->limit(50)
                ->get();
            }
            else{
                Log::info("6666666666666");
                $rows = $factoryAvsData->select(['*'])
                ->where('TimeWeightingT', '>=',date("Y-m-d H:i:s", $timestamp))
                ->whereNotNull("WeightNet")
                ->orderBy("TimeWeightingT", "ASC")
                ->limit(50)
                ->get();
            }
            
            $params = [];
            if($rows && count($rows) > 0){
                Log::info("77777777777777");
                foreach ($rows as $key => $item) {
                    //本地不存在则插入
                    if($this->avs_type == 'toledo'){
                        $params[] = array(
                            'truckno'=>$item['truckno'],
                            'productcode'=>$item['productcode'],
                            'product'=>$item['product'],
                            'firstweight'=>$item['firstweight'],
                            'secondweight'=>$item['secondweight'],
                            'firstdatetime'=>$item['firstdatetime'],
                            'seconddatetime'=>$item['seconddatetime'],
                            'grossdatetime'=>$item['grossdatetime'],
                            'taredatetime'=>$item['taredatetime'],
                            'sender'=>$item['sender'],
                            'transporter'=>$item['transporter'],
                            'receiver'=>$item['receiver'],
                            'gross'=>$item['gross'],
                            'tare'=>$item['tare'],
                            'net'=>$item['net'],
                            'datastatus'=>$item['datastatus'],
                            'weighid'=>$item['id'],
                            'created_at' => $item['taredatetime'],
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                    }
                    else{
                        $params[] = array(
                            'truckno'=>$item['VehicleNo'],
                            'productcode'=>$item['GarbageType'],
                            'product'=>$item['GarbageType'],
                            'firstweight'=>$item['WeightGross'],
                            'secondweight'=>$item['WeightTare'],
                            'firstdatetime'=>$item['TimeWeightingG'],
                            'seconddatetime'=>$item['TimeWeightingT'],
                            'grossdatetime'=>$item['TimeWeightingG'],
                            'taredatetime'=>$item['TimeWeightingT'],
                            'sender'=>$item['Source'],
                            'transporter'=>$item['TransDept'],
                            'receiver'=>$item['DepartmentSTA'],
                            'gross'=>$item['WeightGross'],
                            'tare'=>$item['WeightTare'],
                            'net'=>$item['WeightNet'],
                            'datastatus'=>$item['RecordStatus'],
                            'weighid'=>$item['Id'],
                            'created_at' => $item['TimeWeightingT'],
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                    }
                }
            }

            if($params && count($params) > 0){
                $this->formatData($params);
                Log::info($this->date . '地磅数据插入成功'.count($params).'条');
            }
            else{
                Log::info($this->date . '地磅没有数据插入');
            }
        }
        catch(ErrorException $ex){
            Log::info('连接电厂地磅数据库异常');
            Log::info(var_export($ex, true));
        }
    }

    private function formatData($params){
        $datelist = [];
        $insertlist = [];
        $insertFormatList = [];
        $updatelist = [];
        $updateFormatList = [];

        $WeighBridgeObj = (new WeighBridge())->setConnection($this->tenement_conn)->setTable($this->local_table);
        $WeighBridgeFormatObj = (new WeighBridgeFormat())->setConnection($this->tenement_conn)->setTable($this->local_format_table);
        $WeighBridgeCateSmallObj = (new WeighbridgeCateSmall())->setConnection($this->tenement_conn);

        //查询数据是否存在，不存在则增加，存在则更新
        foreach ($params as $key => $item) {
            $date = date('Y-m-d', strtotime($item['taredatetime']));
            if(!in_array($date, $datelist)){
                $datelist[] = $date;
            }
            $datalist[$key]['created_at'] = date('Y-m-d H:i:s');
            $datalist[$key]['updated_at'] = date('Y-m-d H:i:s');

            //判断小分类是否存在，不存在则新增
            $row_samll_cate = $WeighBridgeCateSmallObj->where('name', $item['product'])->first();
            if(!$row_samll_cate || !isset($row_samll_cate->id)){
                $row_samll_cate = $WeighBridgeCateSmallObj->create([
                    'name' => $item['product']
                ]);
            }

            //查询是否有数据，有则更新，否则新增
            $local_row = $WeighBridgeObj->findByWeighId($item['weighid']);
            if($local_row && isset($local_row->id)){
                //添加到更新数据
                $updatelist[] = $datalist[$key];
                $updateFormatList[] = array(
                    "grossdatetime" => $item['grossdatetime'],
                    "taredatetime" => $item['taredatetime'],
                    "net" => $item['net'],
                    "weighid" => $item['weighid'],
                    "weighbridge_cate_small_id" => $row_samll_cate['id'],
                    'updated_at' => date('Y-m-d H:i:s')
                );
            }
            else{
                //添加到新增数据
                $insertlist[] = $datalist[$key];
                //添加到格式化数据
                $insertFormatList[] = array(
                    'grossdatetime' => $item['grossdatetime'],
                    'taredatetime' => $item['taredatetime'],
                    'net' => $item['net'],
                    'weighid' => $item['weighid'],
                    'weighbridge_cate_small_id' => $row_samll_cate['id'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
            }
        }

        if(!empty($insertlist)){
            DB::beginTransaction();
            try {
                $WeighBridgeObj->insertMany($insertlist);
                $WeighBridgeFormatObj->insertMany($insertFormatList);
                foreach ($updatelist as $key => $item) {
                    $where = array(
                        "weighid" => $item['weighid']
                    );
                    $WeighBridgeObj->updateOne($updatelist[$key], $where);
                    $WeighBridgeFormatObj->updateOne($updateFormatList[$key], $where);

                    //数据状态为删除时，删除数据
                    if($item['datastatus'] == 9){
                        $WeighBridgeObj->destroyByWeighId($item['weighid']);
                        $WeighBridgeFormatObj->destroyByWeighId($item['weighid']);
                    }
                }
                DB::commit();
            } catch (ErrorException $e) {
                DB::rollback();
            }

            //不是今天的要重新统计当天的累积量
            if(!empty($datelist)){
                foreach ($datelist as $k9 => $date) {
                    if($date != date('Y-m-d')){
                        Artisan::queue('count:dayWeighBridgeData', [
                            '--date' => $date
                        ]);
                    }
                }
            }
        }
    }
}
