<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIS\DcsStandard;
use App\Models\SIS\DcsGroup;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BaseExport;
use UtilService;
use Log;

class DcsGroupController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dcs-group",
     *     tags={"DCS分组dcs-group"},
     *     operationId="dcs-group-index",
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
     *         description="关键字中文名搜索",
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
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="DcsGroups",
     *                  description="DcsGroups",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/DcsGroups")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $name = $request->input('name');

        $rows = DcsGroup::select(['*']);
        if ($name) {
            $rows = $rows->where('name', 'like', "%{$name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $item->dcsStandard;
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total]);
    }

    /**
     * @OA\Post(
     *     path="/api/dcs-group",
     *     tags={"DCS分组dcs-group"},
     *     operationId="dcs-group-store",
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
     *         description="名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="description",
     *         in="query",
     *         name="description",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="DcsGroup",
     *                  description="DcsGroup",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/DcsGroup")
     *                  }
     *               )
     *          )
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $input = $request->only(['name', 'description']);
        try {
            $res = DcsGroup::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
    }

    /**
     * @OA\Get(
     *     path="/api/dcs-group/{id}",
     *     tags={"DCS分组dcs-group"},
     *     operationId="dcs-group-show",
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
     *         description="DcsGroup主键",
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
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="DcsGroup",
     *                  description="DcsGroup",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/DcsGroup")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function show($id)
    {
        $row = DcsGroup::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }

        $row->dcsStandard;
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Put(
     *     path="/api/dcs-group/{id}",
     *     tags={"DCS分组dcs-group"},
     *     operationId="dcs-group-update",
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
     *         description="DcsGroup主键",
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
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="update succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="DcsGroup",
     *                  description="DcsGroup",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/DcsGroup")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function update(Request $request, $id)
    {
        $row = DcsGroup::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, ''));
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
     *     path="/api/dcs-group/{id}",
     *     tags={"DCS分组dcs-group"},
     *     operationId="dcs-group-destroy",
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
     *         description="DcsGroup主键",
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
        $row = DcsGroup::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
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
     *     path="/api/dcs-group/bind-relation",
     *     tags={"DCS分组dcs-group"},
     *     operationId="dcs-group-bind-relation",
     *     summary="绑定分组和DCS标准名称关系",
     *     description="使用说明：绑定分组和DCS标准名称关系",
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
     *         description="分组ID",
     *         in="query",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="标准DCS名称ID列表，英文逗号隔开",
     *         in="query",
     *         name="dcs_standard_ids",
     *         required=false,
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
    public function bindRelation(Request $request)
    {
        $input = $request->only(['id', 'dcs_standard_ids']);
        DB::beginTransaction();
        try {
            $dcsGroup = DcsGroup::find($input['id']);
            $final_id_arr = explode(',', $input['dcs_standard_ids']); //新的最终小类ID列表
            $already_in_arr = [];  //已经存在关联关系的小类列表
            $oldDcsStandards = $dcsGroup->dcsStandard;

            //解除旧的关联
            if($oldDcsStandards){
                foreach ($oldDcsStandards as $key => $item) {
                    if(!in_array($item->id, $final_id_arr)){
                        //解除旧的关联
                        $item->dcsGroup()->dissociate($dcsGroup->id);  //解除时用belongsTo方使用dissociate方法
                        $item->save();
                    }
                    else{
                        //已经存在关联关系的小类列表
                        $already_in_arr[] = $item->id;
                    }
                }
            }

            //绑定新的关联
            foreach ($final_id_arr as $key => $dcs_standard_id) {
                if($dcs_standard_id && !in_array($dcs_standard_id, $already_in_arr)){
                    $dcsStandard = DcsStandard::find($dcs_standard_id);
                    $dcsGroup->dcsStandard()->save($dcsStandard);
                    $dcsGroup->save();
                }
            }
            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
    }

    /**
     * @OA\Get(
     *     path="/api/dcs-group/show-relation/{id}",
     *     tags={"DCS分组dcs-group"},
     *     operationId="dcs-group-show-relation",
     *     summary="获取详细DCS标准名称关联信息",
     *     description="使用说明：获取详细DCS标准名称关联信息",
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
     *         description="分组ID",
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
    public function showRelation($id)
    {
        $row = DcsGroup::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        $row->dcsStandard;
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Post(
     *     path="/api/dcs-group/import",
     *     tags={"DCS分组dcs-group"},
     *     operationId="dcs-group-import",
     *     summary="导入多条数据",
     *     description="使用说明：导入多条数据",
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
     *         description="JSON文件",
     *         in="query",
     *         name="json",
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
    public function import(Request $request)
    {
        $json = $request->input('json');
        $arr = json_decode($json, true);
        $header = $arr['header'];
        $results = $arr['results'];
        $obj = new DcsGroup();

        try {
            $params = [];
            foreach ($results as $key => $item) {
                $temp = $item;
                $temp['created_at'] = date('Y-m-d H:i:s');
                $temp['updated_at'] = date('Y-m-d H:i:s');
                $params[] = $temp;
            }
            $obj->insertMany($params);
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/dcs-group/download",
     *     tags={"DCS分组dcs-group"},
     *     operationId="dcs-group-download",
     *     summary="导出多条数据",
     *     description="使用说明：导出多条数据",
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
     *         description="ID列表 多个英文逗号隔开  所有传all",
     *         in="query",
     *         name="ids",
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
    public function download(Request $request)
    {
        ini_set('memory_limit', -1);
        $ids = $request->input('ids');
        if($ids == 'all'){
            $id_arr = explode(',', $ids);
            $final_data = DcsGroup::select(['name', 'description'])->get()->toArray();
        }
        else{
            $id_arr = explode(',', $ids);
            $final_data = DcsGroup::select(['name', 'description'])->whereIn('id', $id_arr)->get()->toArray();
        }
        $headings = ['名称', '描述'];
        array_unshift($final_data, $headings);
        $excel = new BaseExport($final_data, $author='猫小鱼', $sheetname='统一字段名分组');

        return Excel::download($excel, '统一字段名_' . date('YmdHis') . '.xlsx');
    }
}


/**
 * @OA\Definition(
 *     definition="DcsGroups",
 *     type="array",
 *     @OA\Items(ref="#/definitions/DcsGroup")
 * )
 */
