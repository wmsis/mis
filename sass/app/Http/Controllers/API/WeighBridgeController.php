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
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $rows, 'total' => $total]);
    }

    private function validate_factory($factory){
        $tb_list = [];
        $datalist = Orgnization::where('level', 3)->get();
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
