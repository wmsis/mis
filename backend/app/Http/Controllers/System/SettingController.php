<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UtilService;
use App\Models\SIS\SysUserMap;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/setting/sys-user-maps",
     *     tags={"系统设置setting"},
     *     operationId="setting-sys-user-maps",
     *     summary="分页获取系统映射关系列表",
     *     description="使用说明：分页获取系统映射关系列表",
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
     *         description="每页数据量",
     *         in="query",
     *         name="num",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=20,
     *         ),
     *     ),
     *      @OA\Parameter(
     *          description="页数",
     *          in="query",
     *          name="page",
     *          required=false,
     *          @OA\Schema(
     *             type="integer",
     *             default=1,
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="succeed",
     *      ),
     * )
     */
    public function sysUserMaps(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $rows = SysUserMap::select(['*']);
        $total = $rows->count();
        $rows = $rows->orderBy('id', 'desc')->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $basic_user = User::find($item->basic_user_id);
            $target_user = DB::connection('mysql_report')->table('users')->find($item->target_user_id);
            $rows[$key]['basic_user_name'] = $basic_user ? $basic_user->name : '';
            $rows[$key]['target_user_name'] = $target_user ? $target_user->name : '';
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Get(
     *     path="/api/setting/sys-user-list",
     *     tags={"系统设置setting"},
     *     operationId="setting-sys-users",
     *     summary="获取系统用户列表",
     *     description="使用说明：获取系统用户列表",
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
     *         description="系统类型  sis或report",
     *         in="query",
     *         name="type",
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
    public function sysUserList(Request $request)
    {
        $type = $request->input('type');
        if($type == 'sis'){
            $lists = User::where('type', '!=', 'admin')->get();
        }
        elseif($type == 'report'){
            $lists = DB::connection('mysql_report')->table('users')->where('type', '!=', 'admin')->get();
        }
        else{
            $lists = [];
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $lists);
    }

    /**
     * @OA\Post(
     *     path="/api/setting/sys-user-map-store",
     *     tags={"系统设置setting"},
     *     operationId="setting-sys-user-map-store",
     *     summary="保存用户映射关系",
     *     description="使用说明：保存用户映射关系",
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
     *         description="映射表ID",
     *         in="query",
     *         name="id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="基本系统用户ID",
     *         in="query",
     *         name="basic_user_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="目标系统用户ID",
     *         in="query",
     *         name="target_user_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function sysUserMapStore(Request $request){
        $id = $request->input('id');
        $basic_user_id = $request->input('basic_user_id');
        $target_user_id = $request->input('target_user_id');

        try {
            if ($id) {
                $row = SysUserMap::find($id);
                $row->basic_user_id = $basic_user_id;
                $row->target_user_id = $target_user_id;
                $row->save();
            }
            else {
                $params = request(['basic_user_id', 'target_user_id']);
                $params['basic_sys_name'] = 'SIS系统';
                $params['basic_conn_name'] = 'mysql_sis';
                $params['basic_domian'] = 'http://sis.wm-mis.com';
                $params['basic_login_path'] = '/api/auth/login';
                $params['target_sys_name'] = '报表系统';
                $params['target_conn_name'] = 'mysql_report';
                $params['target_domian'] = 'http://rp.wm-mis.com';
                $params['target_login_path'] = '/api/auth/login';
                SysUserMap::create($params); //save 和 create 的不同之处在于 save 接收整个 Eloquent 模型实例而 create 接收原生 PHP 数组
            }
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
        } catch (QueryException $ex) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, $ex->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/setting/sys-user-map-delete",
     *     tags={"系统设置setting"},
     *     operationId="settingDeleteAPI",
     *     summary="删除用户映射接口",
     *     description="使用说明：删除用户映射接口",
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
     *         description="用户映射ID",
     *         in="query",
     *         name="id",
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
    public function sysUserMapDelete(Request $request){
        $id = $request->input('id');
        $row = SysUserMap::find($id);
        if($row){
            $row->delete();
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }
}
