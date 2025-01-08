<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIS\ConfigHistorianDB;
use Illuminate\Database\QueryException;
use App\Http\Requests\API\DcsDbConfigStoreRequest;
use UtilService;
use Log;

class DcsDbConfigController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/dcs-db-config",
     *     tags={"历史数据库配置dcs-db-config"},
     *     operationId="dcs-db-config-store",
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
     *         description="用户名",
     *         in="query",
     *         name="user",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="密码",
     *         in="query",
     *         name="password",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="IP",
     *         in="query",
     *         name="ip",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="端口",
     *         in="query",
     *         name="port",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="版本",
     *         in="query",
     *         name="version",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
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
     *                  property="ConfigHistorianDB",
     *                  description="ConfigHistorianDB",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/ConfigHistorianDB")
     *                  }
     *               )
     *          )
     *     ),
     * )
     */
    public function store(DcsDbConfigStoreRequest $request)
    {
        $input = $request->only(['user', 'password', 'ip', 'port', 'version', 'orgnization_id', 'db_name']);
        $row = ConfigHistorianDB::where('orgnization_id', $input['orgnization_id'])->first();
        if ($row) {
            return UtilService::format_data(self::AJAX_FAIL, '该组织配置数据已存在', '');
        }

        try {
            $res = ConfigHistorianDB::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
    }

    /**
     * @OA\Get(
     *     path="/api/dcs-db-config/{id}",
     *     tags={"历史数据库配置dcs-db-config"},
     *     operationId="dcs-db-config-show",
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
     *                  property="ConfigHistorianDB",
     *                  description="ConfigHistorianDB",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/ConfigHistorianDB")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function show($id)
    {
        $row = ConfigHistorianDB::where('orgnization_id', $id)->first();
        if (!$row) {
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_NO_DATA_MSG, []);
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Put(
     *     path="/api/dcs-db-config/{id}",
     *     tags={"历史数据库配置dcs-db-config"},
     *     operationId="dcs-db-config-update",
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
     *         description="组织ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="用户名",
     *         in="query",
     *         name="user",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="密码",
     *         in="query",
     *         name="password",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="IP",
     *         in="query",
     *         name="ip",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="端口",
     *         in="query",
     *         name="port",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="版本",
     *         in="query",
     *         name="version",
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
     *                  property="ConfigHistorianDB",
     *                  description="ConfigHistorianDB",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/ConfigHistorianDB")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function update(Request $request, $id)
    {
        $row = ConfigHistorianDB::where('orgnization_id', $id)->first();
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, ''));
        }
        $input = $request->input();
        $allowField = ['user', 'password', 'ip', 'port', 'version', 'db_name'];
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
     *     path="/api/dcs-db-config/{id}",
     *     tags={"历史数据库配置dcs-db-config"},
     *     operationId="dcs-db-config-destroy",
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
        $row = ConfigHistorianDB::where('orgnization_id', $id)->first();
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        try {
            $row->forceDelete();
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
    }
}


/**
 * @OA\Definition(
 *     definition="ConfigHistorianDBs",
 *     type="array",
 *     @OA\Items(ref="#/definitions/ConfigHistorianDB")
 * )
 */
