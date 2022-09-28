<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UtilService;
use App\Models\SIS\DcsStandard;
use App\Models\SIS\DcsGroup;
use App\Models\SIS\DcsMap;
use App\Models\Mongo\HistorianData;
use App\Models\Mongo\HistorianFormatData;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Log;

class DcsStandardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dcs-standard/lists",
     *     tags={"DCS标准命名dcs-standard"},
     *     operationId="dcs-standard-lists",
     *     summary="获取所有名称列表",
     *     description="使用说明：获取所有名称列表",
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
     *         description="TAG关键字搜索",
     *         in="query",
     *         name="cn_name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="分组名搜索",
     *         in="query",
     *         name="group_name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *         @OA\JsonContent(
     *             @OA\Property(
	 *              	property="code",
	 *                  description="错误代码，0：为没有错误",
	 *                  type="integer",
	 *					default="0"
	 *             ),
     *             @OA\Property(
	 *                  property="data",
	 *                  description="返回数据",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/DcsStandard")
     *             ),
     *             @OA\Property(
	 *              	property="message",
	 *                  description="错误消息",
	 *                  type="string"
	 *             )
     *         ),
     *     ),
     * )
     */
    public function lists(Request $request)
    {
        $cn_name = $request->input('cn_name');
        $group_name = $request->input('group_name');
        $lists = DB::table('dcs_standard')
            ->join('dcs_group', 'dcs_standard.dcs_group_id', '=', 'dcs_group.id')
            ->select('dcs_standard.*', 'dcs_group.name AS group_name');

        if($cn_name){
            $lists = $lists->where('dcs_standard.cn_name', 'like', "%{$cn_name}%");
        }

        if($group_name){
            $lists = $lists->where('dcs_group.name', 'like', "%{$group_name}%");
        }
        $lists = $lists->whereNull('dcs_standard.deleted_at')->get();
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $lists);
    }

    /**
     * @OA\Get(
     *     path="/api/dcs-standard/datalists",
     *     tags={"DCS标准命名dcs-standard"},
     *     operationId="dcs-standard-datalists",
     *     summary="获取所有数据列表",
     *     description="使用说明：获取所有数据列表",
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
     *         description="标准DCS名称ID列表，多个英文逗号隔开",
     *         in="query",
     *         name="ids",
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
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *         @OA\JsonContent(
     *             @OA\Property(
	 *              	property="code",
	 *                  description="错误代码，0：为没有错误",
	 *                  type="integer",
	 *					default="0"
	 *             ),
     *             @OA\Property(
	 *                  property="data",
	 *                  description="返回数据",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/DcsStandard")
     *             ),
     *             @OA\Property(
	 *              	property="message",
	 *                  description="错误消息",
	 *                  type="string"
	 *             )
     *         ),
     *     ),
     * )
     */
    public function datalists(Request $request)
    {
        $ids = $request->input('ids');
        $start = $request->input('start');
        $end = $request->input('end');
        $id_arr = explode(',', $ids);
        $final = array();
        $lists = DcsStandard::whereIn('id', $id_arr)->get();
        if($lists && count($lists) > 0){
            $local_data_table = 'historian_format_data_' . $this->orgnization->code;
            $obj_hitorian_format_local = (new HistorianFormatData())->setConnection($this->mongo_conn)->setTable($local_data_table);
            foreach ($lists as $key => $item) {
                $group = DcsGroup::find($item->dcs_group_id);
                $lists[$key]['group_name'] = $group && $group->name ? $group->name : '';
                
                $datalist = $obj_hitorian_format_local->where('dcs_standard_id', $item->id)
                    ->where('datetime', '>', $start)
                    ->where('datetime', '<', $end)
                    ->get();

                $lists[$key]['datalist'] = $datalist;
            }
        }

        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $lists);
    }
}
