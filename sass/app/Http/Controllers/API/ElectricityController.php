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
use App\Models\SIS\ElectricityMap;
use App\Http\Requests\API\StoreElectricityRequest;
use Illuminate\Database\QueryException;
use Log;

class ElectricityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/electricity/categories",
     *     tags={"南瑞电表数据electricity"},
     *     operationId="electricity-categories",
     *     summary="获取所有电表分类列表",
     *     description="使用说明：获取所有电表分类列表",
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
     *         description="关键字搜索",
     *         in="query",
     *         name="name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function categories(Request $request)
    {
        $name = $request->input('name');
        if($name){
            $lists = ElectricityMap::where('cn_name', 'like', "%{$name}%")->where('orgnization_id', $this->orgnization->id)->get();
        }
        else{
            $lists = ElectricityMap::where('orgnization_id', $this->orgnization->id)->get();
        }

        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $lists);
    }

    /**
     * @OA\Get(
     *     path="/api/electricity/datalists",
     *     tags={"南瑞电表数据electricity"},
     *     operationId="electricity-datalists",
     *     summary="获取电表数据列表",
     *     description="使用说明：获取电表数据列表",
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
     *         description="电表名称ID列表，多个英文逗号隔开",
     *         in="query",
     *         name="ids",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="开始时间",
     *         in="query",
     *         name="start",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="结束时间",
     *         in="query",
     *         name="end",
     *         required=true,
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
    public function datalists(Request $request)
    {
        $ids = $request->input('ids');
        $start = $request->input('start');
        $end = $request->input('end');
        $id_arr = explode(',', $ids);
        $lists = ElectricityMap::whereIn('id', $id_arr)->get();
        if($lists && count($lists) > 0){
            $table = 'electricity_' . $this->orgnization->code;
            $obj = (new Electricity())->setTable($table);
            foreach ($lists as $key => $item) {
                $datalist = $obj->select(['actual_value as value', 'created_at as datetime'])
                    ->where('electricity_map_id', $item->id)
                    ->where('created_at', '>', $start)
                    ->where('created_at', '<', $end)
                    ->get();

                $lists[$key]['datalist'] = $datalist;
            }
        }

        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $lists);
    }
}
