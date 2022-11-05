<?php
/**
* historian数据获取控制器
*
* @author      cat 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SIS\HistorianTag;
use App\Models\SIS\Orgnization;
use App\Models\SIS\ConfigHistorianDB;
use App\Models\Mongo\HistorianData;
use HistorianService;
use Illuminate\Http\Request;
use UtilService;
use Log;

class HistorianDataController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/historian-data/index",
     *     tags={"历史数据库数据historian data"},
     *     operationId="historian-data-page",
     *     summary="获取DCS数据列表",
     *     description="使用说明：获取DCS数据列表",
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
     *     @OA\Parameter(
     *         description="tag_name 搜索",
     *         in="query",
     *         name="searchTagName",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="succeed",
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
        $searchTagName = $request->input('searchTagName');

        $tb = 'historian_data_' . $factory;
        $obj = (new HistorianData())->setConnection($this->mongo_conn)->setTable($tb);

        if($searchTagName){
            $rows = $obj->select(['*'])->where('tag_name', 'like', "%{$searchTagName}%");
            $total = $obj->where('tag_name', 'like', "%{$searchTagName}%")->count();
        }
        else{
            $rows = $obj->select(['*']);
            $total = $obj->count();
        }
        $rows = $rows->orderBy('datetime', 'desc')->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
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
