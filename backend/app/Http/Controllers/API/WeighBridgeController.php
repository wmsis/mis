<?php
/**
* 地磅数据上报控制器
*
* @author      cat 叶文华
* @version     1.0 版本号
*/

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\SIS\WeighBridge;
use App\Models\SIS\WeighBridgeFormat;
use App\Models\SIS\WeighbridgeCateSmall;
use Illuminate\Support\Facades\DB;
use App\Models\System\Tenement;
use App\Models\SIS\Orgnization;
use Illuminate\Database\QueryException;
use UtilService;
use Log;

class WeighBridgeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/weighbridge/index",
     *     tags={"地磅上报数据weighbridge"},
     *     operationId="weighbridge-index",
     *     summary="获取地磅数据列表",
     *     description="使用说明：获取地磅数据列表",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="query",
     *         name="factory",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="每页数据量",
     *         in="query",
     *         name="num",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=20,
     *         ),
     *     ),
     *      @OA\Parameter(
     *          description="页数",
     *          in="query",
     *          name="page",
     *          required=false,
     *          @OA\Schema(
     *             type="integer",
     *             default=1,
     *         ),
     *      ),
     *      @OA\Parameter(
     *          description="中文名称搜索",
     *          in="query",
     *          name="product",
     *          required=false,
     *          @OA\Schema(
     *             type="string"
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="succeed",
     *          @OA\Schema(
     *               @OA\Property(
     *                   property="WeighBridges",
     *                   description="WeighBridges",
     *                   allOf={
     *                       @OA\Schema(ref="#/definitions/WeighBridge")
     *                   }
     *                )
     *           )
     *      ),
     * )
     */
    public function index(Request $request)
    {
        $factory = $request['factory'];
        if(!$this->validate_factory($factory)){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }

        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;
        $product = $request->input('product');

        $tb = 'weighbridge_' . $factory;
        $WeighBridgeObj = (new WeighBridge())->setTable($tb);

        $rows = $WeighBridgeObj->select(['*']);
        if ($product) {
            $rows = $rows->where('product', 'like', "%{$product}%");
        }
        $total = $rows->count();
        $rows = $rows->orderBy('id', 'desc')->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total]);
    }

    /**
     * @OA\Post(
     *     path="/api/weighbridge/store_multi",
     *     tags={"地磅上报数据weighbridge"},
     *     operationId="weighbridge-multi",
     *     summary="批量新增",
     *     description="使用说明：批量新增",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="租户编码",
     *         in="query",
     *         name="tenement",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="query",
     *         name="factory",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="input",
     *         in="query",
     *         name="input",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *     ),
     * )
     */
    public function store_multi(Request $request)
    {
        $fillable = ['truckno', 'productcode', 'product', 'firstweight', 'secondweight', 'firstdatetime', 'seconddatetime', 'grossdatetime', 'taredatetime',
        'sender', 'transporter', 'receiver', 'gross', 'tare', 'net', 'datastatus', 'weighid'];
        $factory = $request['factory'];
        $tenement = $request['tenement'];
        if(!$this->validate_factory($request['factory'])){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }
        if(!$tenement){
            return UtilService::format_data(self::AJAX_FAIL, '租户信息错误', '');
        }
        else{
            $tenement_info = Tenement::where('code', $tenement);
            if(!$tenement_info){
                return UtilService::format_data(self::AJAX_FAIL, '租户不存在', '');
            }
        }

        $params = $request->only(['input']);
        if (!isset($params['input']) || !$params['input']) {
            return UtilService::format_data(self::AJAX_FAIL, 'input参数错误', '');
        }
        else{
            $datelist = [];
            $formatlist = [];
            $updatelist = [];
            $deletelist = [];
            $datalist = json_decode($params['input'], true);
            foreach ($datalist[0] as $key => $value) {
                if(!in_array($key, $fillable)){
                    return UtilService::format_data(self::AJAX_FAIL, '参数错误', $key);
                }
            }

            $tb = 'weighbridge_' . $factory;
            $tb_format = 'weighbridge_format_' . $factory;
            $WeighBridgeObj = (new WeighBridge())->setConnection($tenement)->setTable($tb);
            $WeighBridgeFormatObj = (new WeighBridgeFormat())->setConnection($tenement)->setTable($tb_format);
            $WeighBridgeCateSmallObj = (new WeighbridgeCateSmall())->setConnection($tenement);

            //查询数据是否存在，不存在则增加
            foreach ($datalist as $key => $item) {
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

                //查询是否有数据，有则更新
                $local_row = $WeighBridgeObj->findByWeighId($item['weighid']);
                if($local_row && isset($local_row->id)){
                    $updatelist[] = $datalist[$key];
                    unset($datalist[$key]);
                }
                else{
                    $formatlist[] = array(
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

            if(!empty($datalist)){
                DB::beginTransaction();
                try {
                    $WeighBridgeObj->insertMany($datalist);
                    $WeighBridgeFormatObj->insertMany($formatlist);
                    foreach ($updatelist as $key => $item) {
                        $where = array(
                            "weighid" => $item['weighid']
                        );
                        $WeighBridgeObj->updateOne($updatelist[$key], $where);

                        if($item['datastatus'] == 0){
                            $WeighBridgeObj->destroyByWeighId($item['weighid']);
                        }
                    }
                    DB::commit();
                } catch (QueryException $e) {
                    DB::rollback();
                    return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, $e->getMessage());
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
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
        }
    }

    private function validate_factory($factory){
        $tb_list = [];
        $datalist = Orgnization::where('level', 2)->get();
        foreach ($datalist as $key => $item) {
            $tb_list[] = $item->code;
        }
        if(!$factory || ($factory && !in_array($factory, $tb_list))){
            return false;
        }
        else{
            return true;
        }
    }
}
