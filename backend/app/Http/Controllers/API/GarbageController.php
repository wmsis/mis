<?php
/**
* 抓斗数据控制器
*
* @author      cat 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIS\GrabGarbage;
use Illuminate\Support\Facades\DB;
use App\Models\SIS\Orgnization;
use Illuminate\Database\QueryException;
use UtilService;
use Log;

class GarbageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/garbage/index",
     *     tags={"抓斗数据garbage"},
     *     operationId="garbage-index",
     *     summary="获取抓斗数据列表",
     *     description="使用说明：获取抓斗数据列表",
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
     *      @OA\Response(
     *          response=200,
     *          description="succeed",
     *          @OA\Schema(
     *               @OA\Property(
     *                   property="GrabGarbages",
     *                   description="GrabGarbages",
     *                   allOf={
     *                       @OA\Schema(ref="#/definitions/GrabGarbage")
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

        $tb = 'grab_garbage_' . $factory;
        $GrabGarbageObj = (new GrabGarbage())->setTable($tb);

        $rows = $GrabGarbageObj->select(['*']);
        $total = $rows->count();
        $rows = $rows->orderBy('id', 'desc')->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total]);
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
