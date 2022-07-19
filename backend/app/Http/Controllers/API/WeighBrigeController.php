<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\SIS\WeighBridge;
use Illuminate\Support\Facades\DB;
use UtilService;

class WeighBrigeController extends Controller
{
    /**
     * @SWG\GET(
     *     path="/api/weighbrige/index",
     *     tags={"weighbrige api"},
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
     *                   property="WeighBriges",
     *                   description="WeighBriges",
     *                   allOf={
     *                       @SWG\Schema(ref="#/definitions/WeighBrige")
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

        if($factory == 'yongqiang2'){
            $WeighBrigeObj = (new WeighBrige())->setTable('weighbridge_yongqiang2');
        }
        else{
            $WeighBrigeObj = null;
        }

        $rows = $WeighBrigeObj->select(['*']);
        if ($cn_name) {
            $rows = $rows->where('product', 'like', "%{$product}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $rows, 'total' => $total]);
    }

    /**
     * @SWG\POST(
     *     path="/api/weighbrige/store_multi",
     *     tags={"weighbrige api"},
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
        if(!$this->validate_factory($request['factory'])){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }

        $params = $request->only(['input']);
        if (!isset($params['input']) || !$params['input']) {
            return UtilService::format_data(self::AJAX_FAIL, 'input参数错误', '');
        }
        else{
            $updatelist = [];
            $datalist = json_decode($params['input'], true);

            if($factory == 'yongqiang2'){
                $WeighBrigeObj = (new WeighBrige())->setTable('weighbrige_yongqiang2');
            }
            else{

            }

            //查询数据是否存在，不存在则增加
            foreach ($datalist as $key => $item) {
                $datalist[$key]['created_at'] = date('Y-m-d H:i:s');
                $datalist[$key]['updated_at'] = date('Y-m-d H:i:s');
                $datalist[$key]['weighid'] = $item['id'];
                $local_row = $WeighBrigeObj->findByWeighId($item['id']);
                unset($datalist[$key]['id']);
                if($local_row){
                    $updatelist[] = $datalist[$key];
                    unset($datalist[$key]);
                }
            }

            DB::beginTransaction();
            try {
                $WeighBrigeObj->insertMany($datalist);
                foreach ($updatelist as $key => $item) {
                    $where = array(
                        "weighid" => $item['weighid']
                    );
                    unset($updatelist[$key]['weighid']);
                    $WeighBrigeObj->updateOne($updatelist[$key], $where);
                }
                DB::commit();
            } catch (QueryException $e) {
                DB::rollback();
                return UtilService::format_data(self::AJAX_FAIL, '操作失败', $e->getMessage());
            }
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        }
    }

    private function validate_factory($factory){
        $tb_list = array('yongqiang2');
        if(!$factory || ($factory && !in_array($factory, $tb_list))){
            return false;
        }
        else{
            return true;
        }
    }
}
