<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\System\Orgnization;
use UtilService;

class OrgnizationController extends Controller
{
    /**
     * @OA\GET(
     *     path="/api/system-orgnizations",
     *     tags={"系统组织orgnization"},
     *     operationId="system-orgnizations-index",
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
     *     @OA\Parameter(
     *         description="组织ID",
     *         in="query",
     *         name="orgnization_id",
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
     *                  property="SystemOrgnizations",
     *                  description="SystemOrgnizations",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/SystemOrgnizations")
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

        $name = $request->input('cn_name');

        $rows = Orgnization::select(['*']);
        if ($name) {
            $rows = $rows->where('cn_name', 'like', "%{$name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $rows, 'total' => $total]);
    }

    /**
     * @OA\POST(
     *     path="/api/system-orgnizations",
     *     tags={"系统组织orgnization"},
     *     operationId="system-orgnizations-store",
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
     *         description="组织名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="所属租户ID",
     *         in="query",
     *         name="tenement_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="组织描述",
     *         in="query",
     *         name="description",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="父组织ID",
     *         in="query",
     *         required=false,
     *         name="parent_id",
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="层级",
     *         in="query",
     *         name="level",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="排序号",
     *         in="query",
     *         name="sort",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="SystemOrgnization",
     *                  description="SystemOrgnization",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/SystemOrgnization")
     *                  }
     *               )
     *          )
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $input = $request->only(['name', 'tenement_id', 'description', 'parent_id', 'level', 'sort']);
        try {
            $res = Orgnization::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
    }

    /**
     * @OA\GET(
     *     path="/api/system-orgnizations/{orgnization}",
     *     tags={"系统组织orgnization"},
     *     operationId="system-orgnizations-show",
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
     *         description="Orgnization ID",
     *         in="path",
     *         name="orgnization",
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
     *                  property="SystemOrgnization",
     *                  description="SystemOrgnization",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/SystemOrgnization")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function show(Orgnization $orgnization)
    {
        if (!$orgnization) {
            return UtilService::format_data(self::AJAX_FAIL, '该数据不存在', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $orgnization);
    }

    /**
     * @OA\PUT(
     *     path="/api/system-orgnizations/{orgnization}",
     *     tags={"系统组织orgnization"},
     *     operationId="system-orgnizations-update",
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
     *         description="Orgnization ID",
     *         in="path",
     *         name="orgnization",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="组织名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="所属租户ID",
     *         in="query",
     *         name="tenement_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="组织描述",
     *         in="query",
     *         name="description",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="父组织ID",
     *         in="query",
     *         required=false,
     *         name="parent_id",
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="层级",
     *         in="query",
     *         name="level",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="排序号",
     *         in="query",
     *         name="sort",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="update succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="SystemOrgnization",
     *                  description="SystemOrgnization",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/SystemOrgnization")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function update(Request $request, Orgnization $orgnization)
    {
        if (!$orgnization) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该数据不存在', ''));
        }
        $input = $request->input();
        $allowField = ['name', 'tenement_id', 'description', 'parent_id', 'level', 'sort'];
        foreach ($allowField as $field) {
            if (key_exists($field, $input)) {
                $inputValue = $input[$field];
                $orgnization[$field] = $inputValue;
            }
        }
        try {
            $orgnization->save();
            $orgnization->refresh();
        } catch (Exception $ex) {
            return UtilService::format_data(self::AJAX_FAIL, '修改失败', $ex->getMessage());
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '修改成功', $orgnization);
    }

    /**
     * @OA\DELETE(
     *     path="/api/system-orgnizations/{orgnization}",
     *     tags={"系统组织orgnization"},
     *     operationId="system-orgnizations-destroy",
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
     *         description="Orgnization ID",
     *         in="path",
     *         name="orgnization",
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
    public function destroy(Orgnization $orgnization)
    {
        if (!$orgnization) {
            return UtilService::format_data(self::AJAX_FAIL, '该数据不存在', '');
        }
        try {
            $orgnization->delete();
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, '删除失败', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '删除成功', '');
    }
}


/**
 * @OA\Definition(
 *     definition="SystemOrgnizations",
 *     type="array",
 *     @OA\Items(ref="#/definitions/SystemOrgnization")
 * )
 */
