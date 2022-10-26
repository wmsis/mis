<?php

namespace App\Http\Controllers\MIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UtilService;
use App\Models\MIS\Alarm;
use App\Models\MIS\AlarmRule;

class AlarmController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/alarm/page",
     *     tags={"报警alarm"},
     *     operationId="alarm-index",
     *     summary="分页获取数据列表",
     *     description="使用说明：分页获取数据列表",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *        )
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
     *     @OA\Parameter(
     *         description="页数",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1,
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="内容",
     *         in="query",
     *         name="content",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;
        $content = $request->input('content');
        $rows = Alarm::select(['*'])->where('orgnization_id', $this->orgnization->id);

        if ($content) {
            $rows = $rows->where('content', 'like', "%{$content}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $item->alarm_rule;
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total]);
    }

    /**
     * @OA\Post(
     *     path="/api/alarm/confirm",
     *     tags={"报警alarm"},
     *     operationId="alarm-confirm",
     *     summary="报警确认",
     *     description="使用说明：报警确认",
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
     *         description="主键",
     *         in="query",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="回执内容",
     *         in="query",
     *         name="remark",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="确认时间 格式如2022-10-01 12:56:56",
     *         in="query",
     *         name="confirm_time",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="update succeed",
     *     ),
     * )
     */
    public function confirm(Request $request)
    {
        $id = $request->input('id');
        $remark = $request->input('remark');
        $confirm_time = $request->input('confirm_time');
        $row = Alarm::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, ''));
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, ''));
        }

        try {
            $row->status = 'complete';
            if($confirm_time){
                $row->confirm_time = $confirm_time;
            }
            if($remark){
                $row->remark = $remark;
            }

            $row->save();
        } catch (Exception $ex) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, $row);
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }
}
