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
use Illuminate\Database\QueryException;
use UtilService;
use Log;

class GarbageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/garbage/categories",
     *     tags={"抓斗数据garbage"},
     *     operationId="garbage-categories",
     *     summary="获取所有垃圾车号斗号料口号",
     *     description="使用说明：获取所有垃圾车号斗号料口号",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
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
        $table = 'grab_garbage_' . $this->orgnization->code;
        $obj = (new GrabGarbage())->setTable($table);

        $che_list = $obj->select(['che'])->groupBy('che')->get();
        $dou_list = $obj->select(['dou'])->groupBy('dou')->get();
        $liao_list = $obj->select(['liao'])->groupBy('liao')->get();

        $che = array(
            'name' => '行车号',
            'values' => []
        );
        foreach ($che_list as $key => $item) {
            $che['values'][] = $item->che;
        }

        $dou = array(
            'name' => '抓斗号',
            'values' => []
        );
        foreach ($dou_list as $key => $item) {
            $dou['values'][] = $item->dou;
        }

        $liao = array(
            'name' => '料口号',
            'values' => []
        );
        foreach ($liao_list as $key => $item) {
            $liao['values'][] = $item->liao;
        }

        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', compact('che', 'dou', 'liao'));
    }

    /**
     * @OA\Get(
     *     path="/api/garbage/datalists",
     *     tags={"抓斗数据garbage"},
     *     operationId="garbage-datalists",
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
     *         description="车号",
     *         in="query",
     *         name="che",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="抓斗号",
     *         in="query",
     *         name="dou",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="料口号",
     *         in="query",
     *         name="liao",
     *         required=false,
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
        $che = $request->input('che');
        $dou = $request->input('dou');
        $liao = $request->input('liao');
        $start = $request->input('start');
        $end = $request->input('end');

        $table = 'grab_garbage_' . $this->orgnization->code;
        $obj = (new GrabGarbage())->setTable($table);
        $datalist = $obj->where('created_at', '>', $start)
            ->where('created_at', '<', $end);

        if($che){
            $datalist = $datalist->where('che', $che);
        }

        if($dou){
            $datalist = $datalist->where('dou', $dou);
        }

        if($liao){
            $datalist = $datalist->where('liao', $liao);
        }

        $datalist = $datalist->get();

        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $datalist);
    }
}
