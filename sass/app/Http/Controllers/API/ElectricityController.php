<?php
/**
* IEC104取得的电表数据控制器
*
* @author      叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UtilService;
use App\Models\SIS\Electricity;
use App\Models\SIS\Orgnization;
use App\Http\Requests\API\StoreElectricityRequest;
use Illuminate\Database\QueryException;
use Log;

class ElectricityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/electricity/index",
     *     tags={"南瑞电表数据electricity"},
     *     operationId="electricity-index",
     *     summary="获取电表数据列表",
     *     description="使用说明：获取电表数据列表",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="query",
     *         name="factory",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
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
     *      ),
     *      @OA\Parameter(
     *          description="页数",
     *          in="query",
     *          name="page",
     *          @OA\Schema(
     *             type="integer",
     *             default=1,
     *          ),
     *          required=false,
     *      ),
     *      @OA\Parameter(
     *          description="中文名称搜索",
     *          in="query",
     *          name="cn_name",
     *          required=false,
     *          @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="succeed",
     *          @OA\Schema(
     *               @OA\Property(
     *                   property="Electricities",
     *                   description="Electricities",
     *                   allOf={
     *                       @OA\Schema(ref="#/definitions/Electricity")
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
        $cn_name = $request->input('cn_name');
        $tb = 'electricity_' . $factory;
        $electricity = (new Electricity())->setTable($tb);

        $rows = $electricity->select(['*']);
        if ($cn_name) {
            $rows = $rows->where('cn_name', 'like', "%{$cn_name}%");
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
