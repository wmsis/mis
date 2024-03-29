<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\Tenement;
use Illuminate\Database\QueryException;
use UtilService;
use MyCacheService;

class TenementController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tenements/lists",
     *     tags={"系统租户tenement"},
     *     operationId="tenements-lists",
     *     summary="获取所有租户列表",
     *     description="使用说明：获取所有租户列表",
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
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="Tenements",
     *                  description="Tenements",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/Tenements")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function lists(Request $request)
    {
        $user = auth('admin')->user();
        $key = UtilService::getKey($user->id, 'TENEMENT');
        $current_tenement = MyCacheService::getCache($key);
        $datalist = Tenement::all();
        foreach ($datalist as $key => $item) {
            if($current_tenement && $current_tenement['code'] == $item->code){
                $datalist[$key]['checked'] = true;
            }
            else{
                $datalist[$key]['checked'] = false;
            }
        }

        if(!$current_tenement){
            $datalist[0]['checked'] = true;
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $datalist);
    }

    /**
     * @OA\Post(
     *     path="/api/tenements/switch",
     *     tags={"系统租户tenement"},
     *     operationId="tenements-switch",
     *     summary="切换租户",
     *     description="使用说明：切换租户",
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
     *         description="租户ID",
     *         in="query",
     *         name="id",
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
    public function switch(Request $request)
    {
        $id = $request->input('id');
        $data = Tenement::find($id);
        if($data){
            $data = $data->toArray();
            $user = auth('admin')->user();
            if($user){
                $key = UtilService::getKey($user->id, 'TENEMENT');
                $expire = auth('admin')->factory()->getTTL() * 60;
                MyCacheService::setCache($key, $data, $expire);
            }
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $data);
        }
        return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
    }

    /**
     * @OA\Get(
     *     path="/api/tenements",
     *     tags={"系统租户tenement"},
     *     operationId="tenements-index",
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
     *         name="username",
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
     *                  property="Tenements",
     *                  description="Tenements",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/Tenements")
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

        $name = $request->input('username');

        $rows = Tenement::select(['*']);
        if ($name) {
            $rows = $rows->where('username', 'like', "%{$name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Post(
     *     path="/api/tenements",
     *     tags={"系统租户tenement"},
     *     operationId="tenements-store",
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
     *         description="ip",
     *         in="query",
     *         name="ip",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="租户名称",
     *         in="query",
     *         name="username",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="编码",
     *         in="query",
     *         name="code",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="数据库名称",
     *         in="query",
     *         name="db_name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="数据库用户名",
     *         in="query",
     *         name="db_user",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="数据库密码",
     *         in="query",
     *         name="db_pwd",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="备注",
     *         in="query",
     *         name="memo",
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
     *                  property="Tenement",
     *                  description="Tenement",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/Tenement")
     *                  }
     *               )
     *          )
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $input = $request->only(['ip', 'username', 'db_user', 'db_pwd', 'code', 'memo', 'db_name']);
        try {
            $res = Tenement::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
    }

    /**
     * @OA\Get(
     *     path="/api/tenements/{tenement}",
     *     tags={"系统租户tenement"},
     *     operationId="tenements-show",
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
     *         description="Tenement ID",
     *         in="path",
     *         name="tenement",
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
     *                  property="Tenement",
     *                  description="Tenement",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/Tenement")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function show(Tenement $tenement)
    {
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $tenement);
    }

    /**
     * @OA\Put(
     *     path="/api/tenements/{tenement}",
     *     tags={"系统租户tenement"},
     *     operationId="tenements-update",
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
     *         description="Tenement ID",
     *         in="path",
     *         name="tenement",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="ip",
     *         in="query",
     *         name="ip",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="租户名称",
     *         in="query",
     *         name="username",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="编码",
     *         in="query",
     *         name="code",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="数据库名称",
     *         in="query",
     *         name="db_name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="数据库用户名",
     *         in="query",
     *         name="db_user",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="数据库密码",
     *         in="query",
     *         name="db_pwd",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="备注",
     *         in="query",
     *         name="memo",
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
     *                  property="Tenement",
     *                  description="Tenement",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/Tenement")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function update(Request $request, Tenement $tenement)
    {
        $input = $request->input();
        $allowField = ['ip', 'username', 'db_user', 'db_pwd', 'code', 'memo', 'db_name'];
        foreach ($allowField as $field) {
            if (key_exists($field, $input)) {
                $inputValue = $input[$field];
                $tenement[$field] = $inputValue;
            }
        }
        try {
            $tenement->save();
            $tenement->refresh();
        } catch (Exception $ex) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, $ex->getMessage());
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $tenement);
    }

    /**
     * @OA\Delete(
     *     path="/api/tenements/{tenement}",
     *     tags={"系统租户tenement"},
     *     operationId="tenements-destroy",
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
     *         description="Tenement ID",
     *         in="path",
     *         name="tenement",
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
    public function destroy(Tenement $tenement)
    {
        try {
            $tenement->delete();
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
    }
}


/**
 * @OA\Definition(
 *     definition="Tenements",
 *     type="array",
 *     @OA\Items(ref="#/definitions/Tenement")
 * )
 */
