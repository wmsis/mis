<?php

namespace App\Http\Controllers\MIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MIS\CheckPointDetail;
use App\Models\MIS\CheckActionDetail;
use App\Models\MIS\CheckActionDetailGroupAllocation;
use App\Models\MIS\CheckActionDetailPersonalAllocation;
use App\Models\MIS\CheckRule;
use App\Models\MIS\CheckRuleAllocation;
use App\Models\MIS\CheckRuleGroup;
use App\Models\MIS\ClassGroupAllocation;
use App\Models\MIS\ClassGroupAllocationDetail;
use App\Models\MIS\ClassSchdule;
use App\Models\MIS\JobStation;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Database\QueryException;
use UtilService;

class CheckController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/check/rule-page",
     *     tags={"考核check"},
     *     operationId="check-rule-page",
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
    public function rulePage(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;
        $remark = $request->input('remark');
        $rows = CheckRule::select(['*'])->where('orgnization_id', $this->orgnization->id);

        if ($remark) {
            $rows = $rows->where('remark', 'like', "%{$remark}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $item->group;
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Post(
     *     path="/api/check/rule-store",
     *     tags={"考核check"},
     *     operationId="check-rule-store",
     *     summary="新增单条考核指标数据",
     *     description="使用说明：新增单条考核指标数据",
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
     *         description="标准名称ID列表，多个英文逗号隔开",
     *         in="query",
     *         name="dcs_standard_ids",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="指标名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="考核分",
     *         in="query",
     *         name="value",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="类型",
     *         in="query",
     *         name="type",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="考核分组类ID",
     *         in="query",
     *         name="check_rule_group_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="是否开启",
     *         in="query",
     *         name="isopen",
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
    public function ruleStore(Request $request)
    {
        $input = $request->only(['name', 'value', 'remark', 'dcs_standard_ids', 'type', 'check_rule_group_id', 'isopen']);
        try {
            $input['orgnization_id'] = $this->orgnization->id;
            $row = CheckRule::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Get(
     *     path="/api/check/rule-show/{id}",
     *     tags={"考核check"},
     *     operationId="check-rule-show",
     *     summary="获取考核指标详细信息",
     *     description="使用说明：获取考核指标详细信息",
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
    public function ruleShow($id)
    {
        $row = CheckRule::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        $row->group;
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Put(
     *     path="/api/check/rule-update/{id}",
     *     tags={"考核check"},
     *     operationId="chack-rule-update",
     *     summary="修改考核指标",
     *     description="使用说明：修改单条考核指标数据",
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
     *         description="标准名称ID列表，多个英文逗号隔开",
     *         in="query",
     *         name="dcs_standard_ids",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="指标名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="考核分",
     *         in="query",
     *         name="value",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="类型",
     *         in="query",
     *         name="type",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="考核分组类ID",
     *         in="query",
     *         name="check_rule_group_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="是否开启",
     *         in="query",
     *         name="isopen",
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
    public function ruleUpdate(Request $request, $id)
    {
        $row = CheckRule::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, ''));
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, ''));
        }

        $input = $request->input();
        $allowField = ['name', 'value', 'remark', 'dcs_standard_ids', 'type', 'check_rule_group_id', 'isopen'];
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
     *     path="/api/check/rule-destroy/{id}",
     *     tags={"考核check"},
     *     operationId="check-destroy",
     *     summary="删除单条考核指标数据",
     *     description="使用说明：删除单条考核指标数据",
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
    public function ruleDestroy($id)
    {
        $row = CheckRule::find($id);
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

    /**
     * @OA\Post(
     *     path="/api/check/rule-job-allocation",
     *     tags={"考核check"},
     *     operationId="check-rule-job-allocation",
     *     summary="保存考核指标岗位分配比例",
     *     description="使用说明：保存考核指标岗位分配比例",
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
     *         description="考核指标ID",
     *         in="query",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="岗位分配详情json数据[{'job_station_id': 1, 'percent': 25}, {'job_station_id': 2, 'percent': 30}]",
     *         in="query",
     *         name="detail",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function ruleJobAllocation(Request $request){
        $id = $request->input('id');
        $detail = $request->input('detail');
        $detail = json_decode($detail, true);
        $check_rule = CheckRule::find($id);
        if(!$check_rule){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        DB::beginTransaction();
        try {
            $check_rule->allocation()->forceDelete();
            $sum = 0;
            if($detail && count($detail) > 0){
                foreach ($detail as $key => $item) {
                    $param = [
                        'check_rule_id' => $check_rule->id,
                        'job_station_id' => $item['job_station_id'],
                        'percent' => $item['percent']
                    ];
                    $sum = $sum + intval($item['percent']);
                    CheckRuleAllocation::create($param);
                }
                if($sum > 100){
                    DB::rollback();
                    return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '分配比例不正确');
                }
            }
            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, $e->getMessage());
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, []);
    }

    /**
     * @OA\Get(
     *     path="/api/check/rule-group-page",
     *     tags={"考核check"},
     *     operationId="check-rule-group-page",
     *     summary="分页获取考核指标分组数据列表",
     *     description="使用说明：分页获取考核指标分组数据列表",
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
     *         name="name",
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
    public function ruleGroupPage(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;
        $name = $request->input('name');
        $rows = CheckRuleGroup::select(['*'])->where('orgnization_id', $this->orgnization->id);

        if ($name) {
            $rows = $rows->where('name', 'like', "%{$remark}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Post(
     *     path="/api/check/rule-group-store",
     *     tags={"考核check"},
     *     operationId="check-rule-group-store",
     *     summary="新增单条考核指标分组数据",
     *     description="使用说明：新增单条考核指标分组数据",
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
     *         description="指标名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="描述",
     *         in="query",
     *         name="description",
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
    public function ruleGroupStore(Request $request)
    {
        $input = $request->only(['name', 'description']);
        try {
            $input['orgnization_id'] = $this->orgnization->id;
            $row = CheckRuleGroup::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Get(
     *     path="/api/check/rule-roup-show/{id}",
     *     tags={"考核check"},
     *     operationId="check-rule-roup-show",
     *     summary="获取考核指标分组详细信息",
     *     description="使用说明：获取考核指标分组详细信息",
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
    public function ruleGroupShow($id)
    {
        $row = CheckRuleGroup::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        $row->group;
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Put(
     *     path="/api/check/rule-roup-update/{id}",
     *     tags={"考核check"},
     *     operationId="chack-rule-roup-update",
     *     summary="修改考核指标分组",
     *     description="使用说明：修改单条考核指标分组数据",
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
     *         description="标准名称ID列表，多个英文逗号隔开",
     *         in="query",
     *         name="dcs_standard_ids",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="指标名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="考核分",
     *         in="query",
     *         name="value",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="类型",
     *         in="query",
     *         name="type",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="考核分组类ID",
     *         in="query",
     *         name="check_rule_group_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="是否开启",
     *         in="query",
     *         name="isopen",
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
    public function ruleGroupUpdate(Request $request, $id)
    {
        $row = CheckRuleGroup::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, ''));
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, ''));
        }

        $input = $request->input();
        $allowField = ['name', 'description'];
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
     *     path="/api/check/rule-group-destroy/{id}",
     *     tags={"考核check"},
     *     operationId="check-rule-group-destroy",
     *     summary="删除单条考核分组数据",
     *     description="使用说明：删除单条考核分组数据",
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
    public function ruleGroupDestroy($id)
    {
        $row = CheckRuleGroup::find($id);
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

    /**
     * @OA\Get(
     *     path="/api/check/job-page",
     *     tags={"考核check"},
     *     operationId="check-job-page",
     *     summary="分页获取岗位数据列表",
     *     description="使用说明：分页获取岗位数据列表",
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
     *         name="name",
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
    public function jobPage(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;
        $name = $request->input('name');
        $rows = JobStation::select(['*'])->where('orgnization_id', $this->orgnization->id);

        if ($name) {
            $rows = $rows->where('name', 'like', "%{$name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $item->group;
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Post(
     *     path="/api/check/job-store",
     *     tags={"考核check"},
     *     operationId="check-job-store",
     *     summary="新增单条岗位数据",
     *     description="使用说明：新增单条岗位数据",
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
     *         description="名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="描述",
     *         in="query",
     *         name="description",
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
    public function jobStore(Request $request)
    {
        $input = $request->only(['name', 'description']);
        try {
            $input['orgnization_id'] = $this->orgnization->id;
            $row = JobStation::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Get(
     *     path="/api/check/job-show/{id}",
     *     tags={"考核check"},
     *     operationId="check-job-show",
     *     summary="获取岗位详细信息",
     *     description="使用说明：获取岗位详细信息",
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
    public function jobShow($id)
    {
        $row = JobStation::find($id);
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
     *     path="/api/check/job-update/{id}",
     *     tags={"考核check"},
     *     operationId="chack-job-update",
     *     summary="修改岗位",
     *     description="使用说明：修改单条岗位数据",
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
     *         description="名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="描述",
     *         in="query",
     *         name="description",
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
    public function jobUpdate(Request $request, $id)
    {
        $row = JobStation::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, ''));
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, ''));
        }

        $input = $request->input();
        $allowField = ['name', 'description'];
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
     *     path="/api/check/job-destroy/{id}",
     *     tags={"考核check"},
     *     operationId="job-destroy",
     *     summary="删除单条岗位数据",
     *     description="使用说明：删除单条岗位数据",
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
    public function jobDestroy($id)
    {
        $row = JobStation::find($id);
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

    /**
     * @OA\Get(
     *     path="/api/check/jobs",
     *     tags={"考核check"},
     *     operationId="check-jobs",
     *     summary="获取电厂所有岗位列表",
     *     description="使用说明：获取电厂所有岗位列表",
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
    public function jobs(Request $request)
    {
        $data = JobStation::where('orgnization_id', $this->orgnization->id)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $data);
    }

    /**
     * @OA\Get(
     *     path="/api/check/class-group-allocation-page",
     *     tags={"考核check"},
     *     operationId="check-class-group-allocation--page",
     *     summary="分页获取班组收入分配数据列表",
     *     description="使用说明：分页获取班组收入分配数据列表",
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
     *         name="name",
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
    public function classGroupAllocationPage(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;
        $name = $request->input('name');
        $rows = ClassGroupAllocation::select(['*'])->where('orgnization_id', $this->orgnization->id);

        if ($name) {
            $rows = $rows->where('class_group_name', 'like', "%{$name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $item->detail;
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Post(
     *     path="/api/check/class-group-allocation-store",
     *     tags={"考核check"},
     *     operationId="check-class-group-allocation-store",
     *     summary="新增单条班组收入分配数据",
     *     description="使用说明：新增单条班组收入分配数据",
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
     *         description="班组名称",
     *         in="query",
     *         name="class_group_name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="是否开启",
     *         in="query",
     *         name="isopen",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="是否开启",
     *         in="query",
     *         name="isopen",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="岗位分配详情json数据[{'job_station_id': 1, 'percent': 25}, {'job_station_id': 2, 'percent': 30}]",
     *         in="query",
     *         name="detail",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *     ),
     * )
     */
    public function classGroupAllocationStore(Request $request)
    {
        $input = $request->only(['class_group_name', 'isopen']);
        $detail = $request->input('detail');
        $detail = json_decode($detail, true);
        DB::beginTransaction();
        try {
            $sum = 0;
            if($detail && count($detail) > 0){
                ClassGroupAllocation::updateOrCreate([
                    'orgnization_id' => $this->orgnization->id,
                    'class_group_name' => $input['class_group_name'],
                ], [
                    'isopen' => $input['isopen']
                ]);

                $row = ClassGroupAllocation::where('orgnization_id', $this->orgnization->id)->where('class_group_name', $input['class_group_name'])->first();
                $row->detail()->forceDelete();
                foreach ($detail as $key => $item) {
                    $param = [
                        'class_group_allocation_id' => $row->id,
                        'job_station_id' => $item['job_station_id'],
                        'percent' => $item['percent']
                    ];
                    $sum = $sum + intval($item['percent']);
                    ClassGroupAllocationDetail::create($param);
                }
                if($sum > 100){
                    DB::rollback();
                    return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '分配比例不正确');
                }
            }
            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, $e->getMessage());
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Get(
     *     path="/api/check/class-group-allocation-show/{id}",
     *     tags={"考核check"},
     *     operationId="check-class-group-allocation-show",
     *     summary="获取班组收入分配详细信息",
     *     description="使用说明：获取班组收入分配详细信息",
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
    public function classGroupAllocationShow($id)
    {
        $row = ClassGroupAllocation::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        $row->detail;
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Delete(
     *     path="/api/check/class-group-allocation-destroy/{id}",
     *     tags={"考核check"},
     *     operationId="class-group-allocation-destroy",
     *     summary="删除单条班组分配",
     *     description="使用说明：删除班组分配",
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
    public function classGroupAllocationDestroy($id)
    {
        $row = JobStation::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        try {
            $row->forceDelete();
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
    }

    /**
     * @OA\Get(
     *     path="/api/check/action-page",
     *     tags={"考核check"},
     *     operationId="check-action-page",
     *     summary="分页获取考核动作（打分）数据列表",
     *     description="使用说明：分页获取考核动作（打分）数据列表",
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
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function actionPage(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;
        $rows = CheckActionDetail::select(['*'])->where('orgnization_id', $this->orgnization->id);
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {

        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Post(
     *     path="/api/check/action-store",
     *     tags={"考核check"},
     *     operationId="check-action-store",
     *     summary="新增单条考核动作（打分）数据",
     *     description="使用说明：新增单条考核动作（打分）数据",
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
     *         description="日期",
     *         in="query",
     *         name="date",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="考核指标ID",
     *         in="query",
     *         name="check_rule_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="考核类型  个人personal或班组group",
     *         in="query",
     *         name="type",
     *         required=true,
     *         @OA\Schema(
     *             type="array",
     *             default="personal",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"personal", "group"},
     *             )
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="班组名",
     *         in="query",
     *         name="class_group_name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="用户名",
     *         in="query",
     *         name="user_id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="岗位分配详情json数据[{'job_station_id': 1, 'percent': 25}, {'job_station_id': 2, 'percent': 30}]",
     *         in="query",
     *         name="detail",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *     ),
     * )
     */
    public function actionStore(Request $request)
    {
        //参数验证
        $user_id = $request->input('user_id');
        $class_group_name = $request->input('class_group_name');
        $detail = json_decode($detail, true);
        $input = $request->only(['check_rule_id', 'type', 'date']);
        if($input['type'] == 'personal'){
            if(!$input['user_id']){
                return UtilService::format_data(self::AJAX_FAIL, '用户ID不能为空', '');
            }
        }
        else{
            if(!$input['class_group_name']){
                return UtilService::format_data(self::AJAX_FAIL, '班组名不能为空', '');
            }
            elseif(!$input['detail']){
                return UtilService::format_data(self::AJAX_FAIL, '分配比例不能为空', '');
            }
        }

        DB::beginTransaction();
        try {
            $check_rule = CheckRule::find($input['check_rule_id']);
            $input['orgnization_id'] = $this->orgnization->id;
            $input['value'] = $check_rule ? $check_rule->value : 0;
            $row = CheckActionDetail::create($input);
            if($input['type'] == 'personal'){
                //分配到个人时
                CheckActionDetailPersonalAllocation::create([
                    'check_action_detail_id' => $row->id,
                    'user_id' => $user_id,
                    'percent' => 100,
                    'value' => $input['value']
                ]);
            }
            else{
                //分配到班组
                $sum = 0;
                if($detail && count($detail) > 0){
                    $class_schdules = ClassSchdule::where('date', $input['date'])->where('class_group_name', $class_group_name)->get();
                    if($class_schdules && count($class_schdules) > 0){
                        //计算所有班组员工的具体分配额度
                        foreach ($class_schdules as $k9 => $class_schdule) {
                            $user = User::find($class_schdule->user_id);
                            $percent = 0;
                            $value = 0;
                            //计算岗位分配额度
                            if($user->job_station_id){
                                //获取用户岗位分配比例
                                foreach ($detail as $k99 => $item) {
                                    if($item['job_station_id'] == $user->job_station_id){
                                        $value = (float)$item['percent'] * (float)$input['value'] * 0.01;
                                        $percent = $item['percent'];
                                        break;
                                    }
                                }

                                //保存分配详情
                                CheckActionDetailGroupAllocation::create([
                                    'check_action_detail_id' => $row->id,
                                    'class_group_name' => $class_group_name,
                                    'job_station_id' => $user->job_station_id,
                                    'user_id' => $class_schdule->user_id,
                                    'percent' => $percent,
                                    'value' => $value
                                ]);

                                //扣分详情
                                CheckPointDetail::create([
                                    'orgnization_id'=>$this->orgnization->id,
                                    'user_id'=>$class_schdule->user_id,
                                    'date'=>$input['date'],
                                    'class_group_name' => $class_group_name,
                                    'value'=>$value,
                                    'reason'=> $check_rule->name ? $check_rule->name : $check_rule->remark,
                                    'type'=> 'daily'
                                ]);
                                $sum = $sum + intval($percent);
                            }
                        }
                    }
                    if($sum > 100){
                        DB::rollback();
                        return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '分配比例不正确');
                    }
                }
            }
            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Get(
     *     path="/api/check/action-show/{id}",
     *     tags={"考核check"},
     *     operationId="check-action-show",
     *     summary="获取考核动作（打分）详细信息",
     *     description="使用说明：获取考核动作（打分）详细信息",
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
    public function actionShow($id)
    {
        $row = CheckActionDetail::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        $row = $row->toArray();
        if($row['type'] == 'personal'){
            $detail = CheckActionDetailPersonalAllocation::where('check_action_detail_id', $row->id)->get();
        }
        else{
            $detail = CheckActionDetailGroupAllocation::where('check_action_detail_id', $row->id)->get();
        }
        $row['detail'] = $detail;

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Get(
     *     path="/api/check/user-rank",
     *     tags={"考核check"},
     *     operationId="check-user-rank",
     *     summary="用户得分排名",
     *     description="使用说明：用户得分排名",
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
     *         description="开始时间",
     *         in="query",
     *         name="start",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="结束时间",
     *         in="query",
     *         name="end",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="班组名",
     *         in="query",
     *         name="class_group_name",
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
    public function userRank(Request $request)
    {
        $final = [];
        $start = $request->input('start');
        $end = $request->input('end');
        $class_group_name = $request->input('class_group_name');
        $lists = CheckPointDetail::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->selectRaw('SUM(value) as val, user_id, date')
            ->groupBy('user_id')
            ->groupBy('date')
            ->get();

        if(count($lists) > 0){
            $lists = $lists->toArray();
            $lists = $this->bubbleSort($lists);
            foreach ($lists as $k9 => $item) {
                $user = User::find($item['user_id']);
                $item['user_name'] = $user ? $user->name : '';
                $final[] = $item;
            }
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $final);
    }

    /**
     * @OA\Get(
     *     path="/api/check/group-rank",
     *     tags={"考核check"},
     *     operationId="check-group-rank",
     *     summary="班组得分排名",
     *     description="使用说明：班组得分排名",
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
     *         description="开始时间",
     *         in="query",
     *         name="start",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="结束时间",
     *         in="query",
     *         name="end",
     *         required=true,
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
    public function groupRank(Request $request)
    {
        $final = [];
        $start = $request->input('start');
        $end = $request->input('end');
        $lists = CheckPointDetail::where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->selectRaw('SUM(value) as val, class_group_name, date')
            ->groupBy('class_group_name')
            ->groupBy('date')
            ->get();

        if(count($lists) > 0){
            $lists = $lists->toArray();
            $lists = $this->bubbleSort($lists);
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $lists);
    }

    //冒泡排序  sort最小的在前
    private function bubbleSort($arr)
    {
        $len = count($arr);
        for ($i = 0; $i < $len -1; $i++) {//循环对比的轮数
            $isChange = false;
            for ($j = 0; $j < $len - $i - 1; $j++) {//当前轮相邻元素循环对比
                if ($arr[$j]['val'] > $arr[$j + 1]['val']) {//如果前边的大于后边的
                    $tmp = $arr[$j];//交换数据
                    $arr[$j] = $arr[$j + 1];
                    $arr[$j + 1] = $tmp;
                    $isChange = true;
                }
            }

            //在一轮排序后，如果没有发生任何改变，说明已经是排序好了，不用在继续后面的循环
            if(!$isChange){
                break;
            }
        }
        return $arr;
    }
}
