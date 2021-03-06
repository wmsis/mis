<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\SIS\WeighBridge;
use Illuminate\Support\Facades\DB;
use UtilService;

class WeighBridgeController extends Controller
{
    /**
     * @SWG\GET(
     *     path="/api/weighbridge/index",
     *     tags={"weighbridge api"},
     *     operationId="",
     *     summary="获取地磅数据列表",
     *     description="使用说明：获取地磅数据列表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="formData",
     *         name="factory",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="每页数据量",
     *         in="query",
     *         name="num",
     *         required=false,
     *         type="integer",
     *         default=20,
     *     ),
     *      @SWG\Parameter(
     *          description="页数",
     *          in="query",
     *          name="page",
     *          required=false,
     *          type="integer",
     *          default=1,
     *      ),
     *      @SWG\Parameter(
     *          description="中文名称搜索",
     *          in="query",
     *          name="product",
     *          required=false,
     *          type="string",
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="succeed",
     *          @SWG\Schema(
     *               @SWG\Property(
     *                   property="WeighBridges",
     *                   description="WeighBridges",
     *                   allOf={
     *                       @SWG\Schema(ref="#/definitions/WeighBridge")
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
        if ($cn_name) {
            $rows = $rows->where('product', 'like', "%{$product}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $rows, 'total' => $total]);
    }

    /**
     * @SWG\POST(
     *     path="/api/weighbridge/store_multi",
     *     tags={"weighbridge api"},
     *     operationId="",
     *     summary="批量新增",
     *     description="使用说明：批量新增",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="formData",
     *         name="factory",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="input",
     *         in="formData",
     *         name="input",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
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
        if(!$this->validate_factory($request['factory'])){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }

        $params = $request->only(['input']);
        if (!isset($params['input']) || !$params['input']) {
            return UtilService::format_data(self::AJAX_FAIL, 'input参数错误', '');
        }
        else{
            $updatelist = [];
            $deletelist = [];
            $datalist = json_decode($params['input'], true);
            foreach ($datalist[0] as $key => $value) {
                if(!in_array($key, $fillable)){
                    return UtilService::format_data(self::AJAX_FAIL, '参数错误', $key);
                }
            }

            $tb = 'weighbridge_' . $factory;
            $WeighBridgeObj = (new WeighBridge())->setTable($tb);

            //查询数据是否存在，不存在则增加
            foreach ($datalist as $key => $item) {
                $datalist[$key]['created_at'] = date('Y-m-d H:i:s');
                $datalist[$key]['updated_at'] = date('Y-m-d H:i:s');
                $local_row = $WeighBridgeObj->findByWeighId($item['weighid']);
                if($local_row){
                    $updatelist[] = $datalist[$key];
                    unset($datalist[$key]);
                }
            }

            DB::beginTransaction();
            try {
                $WeighBridgeObj->insertMany($datalist);
                foreach ($updatelist as $key => $item) {
                    $where = array(
                        "weighid" => $item['weighid']
                    );
                    unset($updatelist[$key]['weighid']);
                    $WeighBridgeObj->updateOne($updatelist[$key], $where);
                }
                DB::commit();
            } catch (QueryException $e) {
                DB::rollback();
                return UtilService::format_data(self::AJAX_FAIL, '操作失败', $e->getMessage());
            }
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', '');
        }
    }

    private function validate_factory($factory){
        $tb_list = config('factory');
        if(!$factory || ($factory && !in_array($factory, $tb_list))){
            return false;
        }
        else{
            return true;
        }
    }
}
