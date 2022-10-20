<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIS\ConfigElectricityDB;
use Illuminate\Database\QueryException;
use UtilService;
use Log;

class ElectricityDbConfigController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/electricity-db-config",
     *     tags={"南瑞电表数据库配置electricity-db-config"},
     *     operationId="electricity-db-config-store",
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
     *         description="主站IP",
     *         in="query",
     *         name="master_ip",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="从站IP",
     *         in="query",
     *         name="slave_ip",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="公共地址",
     *         in="query",
     *         name="common_addr",
     *         required=true,
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
     *         description="store succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="ConfigElectricityDB",
     *                  description="ConfigElectricityDB",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/ConfigElectricityDB")
     *                  }
     *               )
     *          )
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $input = $request->only(['master_ip', 'slave_ip', 'common_addr', 'orgnization_id']);
        $row = ConfigElectricityDB::where('orgnization_id', $input['orgnization_id'])->first();
        if ($row) {
            return UtilService::format_data(self::AJAX_FAIL, '该组织配置数据已存在', '');
        }

        try {
            $res = ConfigElectricityDB::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
    }

    /**
     * @OA\Get(
     *     path="/api/electricity-db-config/{id}",
     *     tags={"南瑞电表数据库配置electricity-db-config"},
     *     operationId="electricity-db-config-show",
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
     *         description="组织ID",
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
     *                  property="ConfigElectricityDB",
     *                  description="ConfigElectricityDB",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/ConfigElectricityDB")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function show($id)
    {
        $row = ConfigElectricityDB::where('orgnization_id', $id)->first();
        if (!$row) {
            return UtilService::format_data(self::AJAX_SUCCESS, '该数据不存在', []);
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Put(
     *     path="/api/electricity-db-config/{id}",
     *     tags={"南瑞电表数据库配置electricity-db-config"},
     *     operationId="electricity-db-config-update",
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
     *         description="主站IP",
     *         in="query",
     *         name="master_ip",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="从站IP",
     *         in="query",
     *         name="slave_ip",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="公共地址",
     *         in="query",
     *         name="common_addr",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="组织ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="update succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="ConfigElectricityDB",
     *                  description="ConfigElectricityDB",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/ConfigElectricityDB")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function update(Request $request, $id)
    {
        $row = ConfigElectricityDB::where('orgnization_id', $id)->first();
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该数据不存在', ''));
        }
        $input = $request->input();
        $allowField = ['master_ip', 'slave_ip', 'common_addr'];
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
            return UtilService::format_data(self::AJAX_FAIL, '修改失败', $ex->getMessage());
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '修改成功', $row);
    }

    /**
     * @OA\Delete(
     *     path="/api/electricity-db-config/{id}",
     *     tags={"南瑞电表数据库配置electricity-db-config"},
     *     operationId="electricity-db-config-destroy",
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
     *         description="组织ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
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
        $row = ConfigElectricityDB::where('orgnization_id', $id)->first();
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, '该数据不存在', '');
        }
        try {
            $row->forceDelete();
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, '删除失败', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '删除成功', '');
    }
}


/**
 * @OA\Definition(
 *     definition="ConfigElectricityDBs",
 *     type="array",
 *     @OA\Items(ref="#/definitions/ConfigElectricityDB")
 * )
 */
