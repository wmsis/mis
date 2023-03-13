<?php

namespace App\Http\Controllers\MIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MIS\CheckTag;
use App\Models\MIS\CheckPointDetail;
use App\Models\User;
use Illuminate\Database\QueryException;
use UtilService;

class CheckController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/check/tag-page",
     *     tags={"考核check"},
     *     operationId="check-tag-page",
     *     summary="分页获取考核指标数据列表",
     *     description="使用说明：分页获取考核指标数据列表",
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
     *         description="关键字标题",
     *         in="query",
     *         name="remark",
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
    public function tagPage(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;
        $remark = $request->input('remark');
        $rows = CheckTag::select(['*'])->where('orgnization_id', $this->orgnization->id);

        if ($remark) {
            $rows = $rows->where('remark', 'like', "%{$remark}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $item->dcsStandard;
            $item->orgnization;
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Post(
     *     path="/api/check/tag-store",
     *     tags={"考核check"},
     *     operationId="check-tag-store",
     *     summary="新增单条数据",
     *     description="使用说明：新增单条数据",
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
     *         description="标准名称ID",
     *         in="query",
     *         name="dcs_standard_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="备注",
     *         in="query",
     *         name="remark",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="每个报警扣去的分数",
     *         in="query",
     *         name="point_every_alarm",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="用户ID列表，英文逗号隔开",
     *         in="query",
     *         name="user_ids",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *     ),
     * )
     */
    public function tagStore(Request $request)
    {
        $input = $request->only(['dcs_standard_id', 'remark', 'point_every_alarm', 'user_ids']);
        try {
            $input['orgnization_id'] = $this->orgnization->id;
            $row = CheckTag::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Get(
     *     path="/api/check/tag-show/{id}",
     *     tags={"考核check"},
     *     operationId="check-show",
     *     summary="获取详细信息",
     *     description="使用说明：获取详细信息",
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
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function tagShow($id)
    {
        $row = CheckTag::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        if($row->user_ids){
            $id_arr = explode(',', $row->user_ids);
            $users = User::whereIn('id', $id_arr)->get();
            $row['users'] = $users;
        }
        else{
            $row['users'] = [];
        }

        $row->dcsStandard;
        $row->orgnization;
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Put(
     *     path="/api/check/tag-update/{id}",
     *     tags={"考核check"},
     *     operationId="chack-tag-update",
     *     summary="修改",
     *     description="使用说明：修改单条数据",
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
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="标准名称ID",
     *         in="query",
     *         name="dcs_standard_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="备注",
     *         in="query",
     *         name="remark",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="每个报警扣去的分数",
     *         in="query",
     *         name="point_every_alarm",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="用户ID列表，英文逗号隔开",
     *         in="query",
     *         name="user_ids",
     *         required=true,
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
    public function tagUpdate(Request $request, $id)
    {
        $row = CheckTag::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, ''));
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, ''));
        }

        $input = $request->input();
        $allowField = ['dcs_standard_id', 'remark', 'point_every_alarm', 'user_ids'];
        foreach ($allowField as $field) {
            if (key_exists($field, $input)) {
                $inputValue = $input[$field];
                $row[$field] = $inputValue;
            }
        }
        try {
            $row->save();
            $row->refresh();
        } catch (Exception $ex) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, $ex->getMessage());
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Delete(
     *     path="/api/check/tag-delete/{id}",
     *     tags={"考核check"},
     *     operationId="check-destroy",
     *     summary="删除单条数据",
     *     description="使用说明：删除单条数据",
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
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="detroy succeed",
     *     ),
     * )
     */
    public function destroy($id)
    {
        $row = CheckTag::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        try {
            $row->delete();
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
    }
}
