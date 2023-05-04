<?php

namespace App\Http\Controllers\MIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MIS\ClassDefine;
use App\Models\MIS\ClassGroup;
use App\Models\MIS\ClassLoop;
use App\Models\MIS\ClassLoopDetail;
use App\Models\MIS\ClassSchdule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\MIS\ClassSchduleRequest;
use App\Http\Requests\MIS\ClassSchduleClearRequest;
use App\Models\SIS\Orgnization;
use UtilService;
use App\Repositories\ClassSchduleRepository;
use Illuminate\Database\QueryException;
use Log;

class ClassController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/class-define/page",
     *     tags={"排班管理class"},
     *     operationId="class-define-page",
     *     summary="分页获取班次数据列表",
     *     description="使用说明：分页获取班次数据列表",
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
     *         description="关键字班次名搜索",
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
    public function definePage(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $name = $request->input('name');
        $rows = ClassDefine::select(['*'])->where('orgnization_id', $this->orgnization->id);

        if ($name) {
            $rows = $rows->where('name', 'like', "%{$name}%");
        }

        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $orgnization = Orgnization::find($this->orgnization->id);
            $rows[$key]['orgnization'] = $orgnization ? $orgnization->toArray() : null;
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Post(
     *     path="/api/class-define/store",
     *     tags={"排班管理class"},
     *     operationId="class-define-store",
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
     *         description="班次名字",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="起始时间",
     *         in="query",
     *         name="start",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="截止时间",
     *         in="query",
     *         name="end",
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
    public function defineStore(Request $request)
    {
        $input = $request->only(['name', 'start', 'end']);
        //判断是否有其他相同的名称
        $data = ClassDefine::where('name', $input['name'])->where('orgnization_id', $this->orgnization->id)->first();
        if($data && $data->name){
            return UtilService::format_data(self::AJAX_FAIL, '班次名称已存在', '');
        }

        try {
            $input['orgnization_id'] = $this->orgnization->id;
            $res = ClassDefine::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
    }

    /**
     * @OA\Get(
     *     path="/api/class-define/show/{id}",
     *     tags={"排班管理class"},
     *     operationId="class-define-show",
     *     summary="获取详班次细信息",
     *     description="使用说明：获取班次详细信息",
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
     *         description="班次ID",
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
    public function defineShow($id)
    {
        $row = ClassDefine::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Put(
     *     path="/api/class-define/update/{id}",
     *     tags={"排班管理class"},
     *     operationId="class-define-update",
     *     summary="修改班次",
     *     description="使用说明：修改班次单条数据",
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
     *         description="班次ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="中文名字",
     *         in="query",
     *         name="name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="设备属性ID",
     *         in="query",
     *         name="device_property_id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="巡检内容",
     *         in="query",
     *         name="content",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="巡检标准",
     *         in="query",
     *         name="standard",
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
    public function defineUpdate(Request $request, $id)
    {
        $row = ClassDefine::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, ''));
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, ''));
        }

        $input = $request->input();
        //判断是否有其他相同的名称
        $data = ClassDefine::where('name', $input['name'])->where('orgnization_id', $this->orgnization->id)->first();
        if($data && $data->id != $id){
            return UtilService::format_data(self::AJAX_FAIL, '班次名称已存在', '');
        }

        $allowField = ['name', 'orgnization_id', 'start', 'end'];
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
     *     path="/api/class-define/destroy/{id}",
     *     tags={"排班管理class"},
     *     operationId="class-define-destroy",
     *     summary="删除班次单条数据",
     *     description="使用说明：删除班次单条数据",
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
     *         description="班次ID",
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
    public function defineDestroy($id)
    {
        $row = ClassDefine::find($id);
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
     *     path="/api/class-group/page",
     *     tags={"排班管理class"},
     *     operationId="class-group-page",
     *     summary="分页获取班组数据列表",
     *     description="使用说明：分页获取班组数据列表",
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
     *         description="关键字班组名搜索",
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
    public function groupPage(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;
        $name = $request->input('name');
        $rows = ClassGroup::select(['*'])->where('orgnization_id', $this->orgnization->id);
        if ($name) {
            $rows = $rows->where('name', 'like', "%{$name}%");
        }

        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $orgnization = Orgnization::find($this->orgnization->id);
            $rows[$key]['orgnization'] = $orgnization ? $orgnization->toArray() : null;
            $charge_user = User::find($item->charge_user_id);
            $rows[$key]['charge_user'] = $charge_user ? $charge_user->toArray() : null;
            $user_id_arr = $item->user_ids ? explode(',', $item->user_ids) : [];
            $other_user = User::whereIn('id', $user_id_arr)->get();
            $rows[$key]['other_user'] = $other_user && count($other_user) > 0 ? $other_user->toArray() : [];
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Post(
     *     path="/api/class-group/store",
     *     tags={"排班管理class"},
     *     operationId="class-group-store",
     *     summary="新增班组单条数据",
     *     description="使用说明：新增班组单条数据",
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
     *         description="班组名字",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="值长用户ID",
     *         in="query",
     *         name="charge_user_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="组员用户ID，英文逗号隔开",
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
    public function groupStore(Request $request)
    {
        $input = $request->only(['name', 'charge_user_id', 'user_ids']);
        //判断是否有其他相同的名称
        $data = ClassGroup::where('name', $input['name'])->where('orgnization_id', $this->orgnization->id)->first();
        if($data && $data->name){
            return UtilService::format_data(self::AJAX_FAIL, '班组名称已存在', '');
        }

        DB::beginTransaction();
        try {
            $input['orgnization_id'] = $this->orgnization->id;
            $class_group = ClassGroup::create($input);

            $charge_user = User::find($input['charge_user_id']);
            if($charge_user){
                $charge_user->class_group_id = $class_group->id;
                $charge_user->save();
            }
            $user_id_arr = explode(',', $input['user_ids']);
            if(!empty($user_id_arr)){
                foreach ($user_id_arr as $key => $user_id) {
                    $other_user = User::find($user_id);
                    if($other_user){
                        $other_user->class_group_id = $class_group->id;
                        $other_user->save();
                    }
                }
            }
            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $class_group);
    }

    /**
     * @OA\Get(
     *     path="/api/class-group/show/{id}",
     *     tags={"排班管理class"},
     *     operationId="class-group-show",
     *     summary="获取班组详细信息",
     *     description="使用说明：获取班组详细信息",
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
     *         description="班组ID",
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
    public function groupShow($id)
    {
        $row = ClassGroup::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Put(
     *     path="/api/class-group/update/{id}",
     *     tags={"排班管理class"},
     *     operationId="class-group-update",
     *     summary="班组修改",
     *     description="使用说明：修改班组单条数据",
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
     *         description="班组ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="班组名字",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="值长用户ID",
     *         in="query",
     *         name="班组",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="组员用户ID，英文逗号隔开",
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
    public function groupUpdate(Request $request, $id)
    {
        $row = ClassGroup::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, ''));
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, ''));
        }

        $input = $request->input();

        //判断是否有其他相同的名称
        $data = ClassGroup::where('name', $input['name'])->where('orgnization_id', $this->orgnization->id)->first();
        if($data && $data->id != $id){
            return UtilService::format_data(self::AJAX_FAIL, '班组名称已存在', '');
        }

        $allowField = ['name', 'charge_user_id', 'orgnization_id', 'user_ids'];
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
     *     path="/api/class-group/destroy/{id}",
     *     tags={"排班管理class"},
     *     operationId="class-group-destroy",
     *     summary="删除班组单条数据",
     *     description="使用说明：删除班组单条数据",
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
     *         description="班组ID",
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
    public function groupDestroy($id)
    {
        $row = ClassGroup::find($id);
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
     *     path="/api/class-group/users",
     *     tags={"排班管理class"},
     *     operationId="class-group-users",
     *     summary="获取班组用户列表",
     *     description="使用说明：获取班组用户列表",
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
     *         description="班组ID，多个英文逗号隔开",
     *         in="query",
     *         name="id",
     *         required=false,
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
    public function groupUsers(Request $request)
    {
        $final = [];
        $id_arr = [];
        $id = $request->input('id');
        if($id){
            $id_arr = explode(',', $id);
        }
        if(!empty($id_arr)){
            $group_rows = ClassGroup::whereIn('id', $id_arr)->where('orgnization_id', $this->orgnization->id)->get();
        }
        else{
            $group_rows = ClassGroup::where('orgnization_id', $this->orgnization->id)->get();
        }

        foreach ($group_rows as $key => $item) {
            $user_id_arr = [];
            if($item->user_ids){
                $user_id_arr = explode(',', $item->user_ids);
            }
            if($item->charge_user_id){
                $user_id_arr[] = $item->charge_user_id;
            }

            if(!empty($user_id_arr)){
                $users = User::whereIn('id', $user_id_arr)->get();
                if(count($users) > 0){
                    $users = $users->toArray();
                    $final = array_merge($final, $users);
                }
            }
        }
        if (empty($final)) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $final);
    }

    /**
     * @OA\Get(
     *     path="/api/class-group/lists",
     *     tags={"排班管理class"},
     *     operationId="class-group-lists",
     *     summary="获取班组列表",
     *     description="使用说明：获取班组列表",
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
     *         description="班组ID，多个英文逗号隔开",
     *         in="query",
     *         name="id",
     *         required=false,
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
    public function groupLists(Request $request)
    {
        $id_arr = [];
        $id = $request->input('id');
        if($id){
            $id_arr = explode(',', $id);
        }
        if(!empty($id_arr)){
            $group_rows = ClassGroup::whereIn('id', $id_arr)->where('orgnization_id', $this->orgnization->id)->get();
        }
        else{
            $group_rows = ClassGroup::where('orgnization_id', $this->orgnization->id)->get();
        }

        foreach ($group_rows as $key => $item) {
            $orgnization = Orgnization::find($this->orgnization->id);
            $group_rows[$key]['orgnization'] = $orgnization ? $orgnization->toArray() : null;
            $charge_user = User::find($item->charge_user_id);
            $group_rows[$key]['charge_user'] = $charge_user ? $charge_user->toArray() : null;
            $user_id_arr = $item->user_ids ? explode(',', $item->user_ids) : [];
            $other_user = User::whereIn('id', $user_id_arr)->get();
            $group_rows[$key]['other_user'] = $other_user && count($other_user) > 0 ? $other_user->toArray() : [];
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $group_rows);
    }

    /**
     * @OA\Get(
     *     path="/api/class-loop/page",
     *     tags={"排班管理class"},
     *     operationId="class-loop-page",
     *     summary="分页获取班次数据列表",
     *     description="使用说明：分页获取班次数据列表",
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
     *         description="关键字排班周期名搜索",
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
    public function loopPage(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;
        $name = $request->input('name');
        $rows = ClassLoop::select(['*'])->where('orgnization_id', $this->orgnization->id);
        if ($name) {
            $rows = $rows->where('name', 'like', "%{$name}%");
        }

        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $orgnization = Orgnization::find($this->orgnization->id);
            $rows[$key]['orgnization'] = $orgnization ? $orgnization->toArray() : null;
            $detail = $item->detail;
            if($detail && count($detail) > 0){

            }
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Post(
     *     path="/api/class-loop/store",
     *     tags={"排班管理class"},
     *     operationId="class-loop-store",
     *     summary="新增排班周期单条数据",
     *     description="使用说明：新增排班周期单条数据",
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
     *         description="排班周期名字",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="周期天数",
     *         in="query",
     *         name="loop_days",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="详情 json数据[{'sort': 1, 'class_define_name': '早班', 'class_define_time': '07:30-16:00'}, {'sort': 2, 'class_define_name': '中班', 'class_define_time': '16:00-23:00'}]",
     *         in="query",
     *         name="detail",
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
    public function loopStore(Request $request)
    {
        $input = $request->only(['name', 'loop_days']);
        $detail = $request->input('detail');
        $detail = json_decode($detail, true);
        DB::beginTransaction();
        try {
            $input['orgnization_id'] = $this->orgnization->id;
            $res = ClassLoop::create($input);
            if($detail && count($detail) > 0){
                foreach ($detail as $key => $item) {
                    $param = [
                        'class_loop_id' => $res->id,
                        'sort' => $item['sort'],
                        'class_define_name' => $item['class_define_name'],
                        'class_define_time' => $item['class_define_time'],
                    ];
                    ClassLoopDetail::create($param);
                }
            }
            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
    }

    /**
     * @OA\Get(
     *     path="/api/class-loop/show/{id}",
     *     tags={"排班管理class"},
     *     operationId="class-loop-show",
     *     summary="获取排班周期详细信息",
     *     description="使用说明：获取排班周期详细信息",
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
     *         description="排班周期ID",
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
    public function loopShow($id)
    {
        $row = ClassLoop::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }
        $detail = $row->detail;
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Put(
     *     path="/api/class-loop/update/{id}",
     *     tags={"排班管理class"},
     *     operationId="class-loop-update",
     *     summary="修改排班周期",
     *     description="使用说明：修改排班周期单条数据",
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
     *         description="排班周期ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="排班周期名称",
     *         in="query",
     *         name="name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="序号",
     *         in="query",
     *         name="sort",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="周期天数",
     *         in="query",
     *         name="loop_days",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="详情 json数据[{'sort': 1, 'class_define_name': '早班', 'class_define_time': '07:30-16:00'}, {'sort': 2, 'class_define_name': '中班', 'class_define_time': '16:00-23:00'}]",
     *         in="query",
     *         name="detail",
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
    public function loopUpdate(Request $request, $id)
    {
        $detail = $request->input('detail');
        $detail = json_decode($detail, true);
        $row = ClassLoop::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, ''));
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, ''));
        }

        $input = $request->input();
        $allowField = ['name', 'show', 'loop_days'];
        DB::beginTransaction();
        try {
            foreach ($allowField as $field) {
                if (key_exists($field, $input)) {
                    $inputValue = $input[$field];
                    $row[$field] = $inputValue;
                }
            }
            $row->save();

            ClassLoopDetail::where('class_loop_id', $id)->forceDelete();
            if($detail && count($detail) > 0){
                foreach ($detail as $key => $item) {
                    $param = [
                        'class_loop_id' => $id,
                        'sort' => $item['sort'],
                        'class_define_name' => $item['class_define_name'],
                        'class_define_time' => $item['class_define_time'],
                    ];
                    ClassLoopDetail::create($param);
                }
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, $ex->getMessage());
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Delete(
     *     path="/api/class-loop/destroy/{id}",
     *     tags={"排班管理class"},
     *     operationId="class-loop-destroy",
     *     summary="删除排班周期单条数据",
     *     description="使用说明：删除排班周期单条数据",
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
     *         description="InspectRule主键",
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
    public function loopDestroy($id)
    {
        $row = ClassLoop::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        DB::beginTransaction();
        try {
            $row->delete();
            ClassLoopDetail::where('class_loop_id', $id)->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
    }

    /**
     * @OA\Get(
     *     path="/api/class-loop/lists",
     *     tags={"排班管理class"},
     *     operationId="class-loop-lists",
     *     summary="获取班组周期列表",
     *     description="使用说明：获取班组周期列表",
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
     *         description="周期ID，多个英文逗号隔开",
     *         in="query",
     *         name="id",
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
    public function groupLoopLists(Request $request)
    {
        $id_arr = [];
        $id = $request->input('id');
        if($id){
            $id_arr = explode(',', $id);
        }

        if(!empty($id_arr)){
            $group_rows = ClassLoop::whereIn('id', $id_arr)->where('orgnization_id', $this->orgnization->id)->get();
        }
        else{
            $group_rows = ClassLoop::where('orgnization_id', $this->orgnization->id)->get();
        }

        foreach ($group_rows as $key => $item) {
            $orgnization = Orgnization::find($this->orgnization->id);
            $group_rows[$key]['orgnization'] = $orgnization ? $orgnization->toArray() : null;
            $detail = $item->detail;
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $group_rows);
    }

    /**
     * @OA\Post(
     *     path="/api/class-schdule/setting",
     *     tags={"排班管理class"},
     *     operationId="class-schdule-setting",
     *     summary="排班设置",
     *     description="使用说明：排班设置",
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
     *         description="日期类型 按天排班或按周期排班  single或multi",
     *         in="query",
     *         name="date_type",
     *         required=true,
     *         @OA\Schema(
     *             type="array",
     *             default="single",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"single", "multi"},
     *             )
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="排班类型 按人排班或按班组排班  person或group",
     *         in="query",
     *         name="class_type",
     *         required=true,
     *         @OA\Schema(
     *             type="array",
     *             default="person",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"person", "group"},
     *             )
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="开始日期",
     *         in="query",
     *         name="date",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="排班月数",
     *         in="query",
     *         name="month",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="排班周期ID date_type为multi时",
     *         in="query",
     *         name="class_loop_id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="排班班次名称 date_type为single时",
     *         in="query",
     *         name="class_define_name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="用户ID 按人排class_type为person班时",
     *         in="query",
     *         name="user_id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="班组名称 按班组排class_type为group班时",
     *         in="query",
     *         name="class_group_name",
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
    public function schduleSetting(ClassSchduleRequest $request)
    {
        //参数
        $date_type = $request->input('date_type');
        $class_type = $request->input('class_type');
        $date = $request->input('date');
        $month = $request->input('month');
        $class_define_name = $request->input('class_define_name');
        $class_loop_id = $request->input('class_loop_id');
        $user_id = $request->input('user_id');
        $class_group_name = $request->input('class_group_name');
        $params = $request->only(['date_type', 'class_type', 'date', 'month', 'class_define_name', 'class_loop_id', 'user_id', 'class_group_name']);

        $user = $user_id ? User::find($user_id) : null;
        Log::info('aaaaaaaaaaaaa');
        Log::info(var_export($user, true));
        $class_group = $user ? $user->classGroup : null;
        if(!$class_group){
            return UtilService::format_data(self::AJAX_FAIL, '请为用户绑定班组', '');
        }

        $class = $this->getClassInfoByName($class_define_name);
        $params['start'] = $class['start'];
        $params['end'] = $class['end'];
        $params['user_class_group'] = $class_group;
        Log::info('bbbbbbbbbbbbbbbb');
        Log::info(var_export($class_group, true));

        if($params['date_type'] == 'single'){
            if(!$class_define_name){
                return UtilService::format_data(self::AJAX_FAIL, '班次不能为空', '');
            }
        }
        else{
            if(!$class_loop_id){
                return UtilService::format_data(self::AJAX_FAIL, '排班周期ID不能为空', '');
            }
            if(!$month){
                return UtilService::format_data(self::AJAX_FAIL, '排班月数不能为空', '');
            }
        }

        if($params['class_type'] == 'person'){
            //按人排班
            if(!$user_id){
                return UtilService::format_data(self::AJAX_FAIL, '用户ID不能为空', '');
            }

            $this->setClassByUser($params);
        }
        else{
            //按班组排班
            if(!$class_group_name){
                return UtilService::format_data(self::AJAX_FAIL, '班组名称不能为空', '');
            }

            $this->setClassByGroup($params);
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
    }

    /**
     * @OA\Get(
     *     path="/api/class-schdule/user-lists",
     *     tags={"排班管理class"},
     *     operationId="class-schdule-user-lists",
     *     summary="用户排班列表",
     *     description="使用说明：用户排班列表",
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
     *         description="月份如 2023-05",
     *         in="query",
     *         name="month",
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
    public function schduleUserLists(Request $request)
    {
        //参数
        $class_type = $request->input('class_type');
        $month = $request->input('month');
        $final = [];

        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));
        $user_lists = ClassSchdule::select(['user_id'])
            ->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->where('orgnization_id', $this->orgnization->id)
            ->groupBy('user_id')
            ->get();

        foreach ($user_lists as $k1 => $item) {
            $schdule_lists = ClassSchdule::select(['date', 'class_define_name', 'start', 'end', 'class_group_name'])
                ->where('date', '>=', $start)
                ->where('date', '<=', $end)
                ->where('user_id', $item['user_id'])
                ->orderBy('date', 'ASC')
                ->get();

            $format_data = [];
            foreach ($schdule_lists as $k9 => $schdule) {
                $date = $schdule['date'];
                unset($schdule['date']);
                $format_data[$date] = $schdule;
            }

            $user = User::select(['id', 'name', 'mobile'])->where('id', $item['user_id'])->first();
            $final[] = [
                'user'=>$user ? $user->toArray() : null,
                'data' => $format_data
            ];
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $final);
    }

    /**
     * @OA\Get(
     *     path="/api/class-schdule/date-lists",
     *     tags={"排班管理class"},
     *     operationId="class-schdule-date-lists",
     *     summary="排班日期列表",
     *     description="使用说明：排班日期列表",
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
     *         description="月份如 2023-05",
     *         in="query",
     *         name="month",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="班次",
     *         in="query",
     *         name="class_define_name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="班组",
     *         in="query",
     *         name="class_group_name",
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
    public function schduleDateLists(Request $request)
    {
        $month = $request->input('month');
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));
        $class_define_name = $request->input('class_define_name');
        $class_group_name = $request->input('class_group_name');
        $final = [];
        $cass_define = config('class.cass_define');

        $timestamp = strtotime($start);
        //按日期获取排班人员信息
        while($timestamp <= strtotime($end)){
            $date = date('Y-m-d', $timestamp);
            foreach ($cass_define as $key => $class) {
                //有传具体班次,只返回具体班次排班
                if($class_define_name){
                    if($class_define_name == $class['name']){
                        $final[] = $this->getSchduleInfo($this->orgnization->id, $date, $class['name'], $class_group_name);
                        break;
                    }
                }
                //返回早白中班信息
                else{
                    if($class['name'] != '休息'){
                        $final[] = $this->getSchduleInfo($this->orgnization->id, $date, $class['name'], $class_group_name);
                    }
                }
            }

            $timestamp = $timestamp + 24 * 60 * 60;
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $final);
    }

    /**
     * @OA\Post(
     *     path="/api/class-schdule/clear",
     *     tags={"排班管理class"},
     *     operationId="class-schdule-clear",
     *     summary="排班清除",
     *     description="使用说明：排班清除",
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
     *         description="开始日期",
     *         in="query",
     *         name="start",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="截止日期",
     *         in="query",
     *         name="end",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="排班类型 按人排班或按班组排班  person或group",
     *         in="query",
     *         name="class_type",
     *         required=true,
     *         @OA\Schema(
     *             type="array",
     *             default="person",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"person", "group"},
     *             )
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="用户ID 按人排class_type为person班时",
     *         in="query",
     *         name="user_id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="班组名称 按班组排class_type为group班时",
     *         in="query",
     *         name="class_group_name",
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
    public function schduleClear(ClassSchduleClearRequest $request)
    {
        $class_chdule_obj = new ClassSchduleRepository();
        $params = $request->only(['class_type', 'start', 'end', 'user_id', 'class_group_name']);
        if($params['class_type'] == 'person'){
            //按人删除排班
            if(!$params['user_id']){
                return UtilService::format_data(self::AJAX_FAIL, '用户ID不能为空', '');
            }
            $class_chdule_obj->deleteByUser($params);
        }
        else{
            //按班组删除排班
            if(!$params['class_group_name']){
                return UtilService::format_data(self::AJAX_FAIL, '班组名称不能为空', '');
            }
            $class_chdule_obj->deleteByGroup($params);
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
    }

    //获取组织某天的排班信息
    private function getSchduleInfo($orgnization_id, $date, $class_define_name, $class_group_name){
        $orgnization = $orgnization_id ? Orgnization::find($orgnization_id) : null;
        $charge_user = DB::table('users')
            ->join('class_schdule', 'users.id', '=', 'class_schdule.user_id')
            ->select(['users.name', 'class_schdule.class_group_name'])
            ->where('class_schdule.date', $date)
            ->where('class_schdule.class_define_name', $class_define_name)
            ->where('class_schdule.is_charge', 1)
            ->whereNull('class_schdule.deleted_at');

        if($orgnization_id){
            $charge_user = $charge_user->where('class_schdule.orgnization_id', $orgnization_id);
        }

        if($class_group_name){
            $charge_user = $charge_user->where('class_schdule.class_group_name', $class_group_name);
        }
        $charge_user = $charge_user->first();

        $other_users = DB::table('users')
            ->join('class_schdule', 'users.id', '=', 'class_schdule.user_id')
            ->select(['users.name'])
            ->where('class_schdule.date', $date)
            ->where('class_schdule.class_define_name', $class_define_name)
            ->where('class_schdule.is_charge', 0)
            ->whereNull('class_schdule.deleted_at');

        if($orgnization_id){
            $other_users = $other_users->where('class_schdule.orgnization_id', $orgnization_id);
        }

        if($class_group_name){
            $other_users = $other_users->where('class_schdule.class_group_name', $class_group_name);
        }
        $other_users = $other_users->get();

        $other_user_name_arr = [];
        $other_user_names = '';
        foreach ($other_users as $key => $other_user) {
            $other_user_name_arr[] = $other_user->name;
        }
        $other_user_names = implode(',', $other_user_name_arr);

        $data = [
            'orgnization_name' => $orgnization ? $orgnization->name : '',
            'date' => $date,
            'class_define_name' => $class_define_name,
            'class_group_name' => $charge_user ? $charge_user->class_group_name : '',
            'charge_user_name' => $charge_user ? $charge_user->name : '',
            'user_names' => $other_user_names
        ];

        return $data;
    }

    //根据用户设置排班
    private function setClassByUser($params){
        Log::info('0000000000000000');
        Log::info(var_export($params, true));
        $class_group = null;
        if($params['user_class_group'] ){
            $class_group = ClassGroup::where('name', $params['user_class_group']->name)->first();
        }
        //只排一天班
        if($params['date_type'] == 'single'){
            $where = [
                'orgnization_id'=>$this->orgnization->id,
                'user_id'=>$params['user_id'],
                'date'=>$params['date']
            ];
            $values = [
                'class_define_name'=>$params['class_define_name'],
                'start'=>$params['start'],
                'end'=>$params['end'],
                'is_charge'=>$class_group && $class_group->charge_user_id == $params['user_id'] ? 1 : 0,//值长
                'class_group_name'=>$params['user_class_group'] ? $params['user_class_group']->name : ''
            ];
            ClassSchdule::updateOrCreate($where, $values);
        }
        //周期排班
        else{
            $loop = ClassLoop::find($params['class_loop_id']);
            if($loop){
                $loop_detail = $loop->detail()->orderBy('sort', 'ASC')->get();
                if($params['month']){
                    $temp_timestamp = strtotime($params['date']) + 30 * $params['month'] * 24 * 60 * 60;
                    $last_date = date('Y-m-t', $temp_timestamp);
                }
                else{
                    $last_date = date('Y-m-t', strtotime($params['date']));
                }
                $timestamp = strtotime($params['date']);
                DB::beginTransaction();
                try {
                    //循环排班到月底
                    if($loop_detail && count($loop_detail) > 0){
                        $loop_detail = $loop_detail->toArray();
                        Log::info('11111111111');
                        Log::info(var_export($loop_detail, true));
                        $index = 0;
                        while($timestamp <= strtotime($last_date)){
                            $yushu = $index%8; //求余
                            $class_detail = $loop_detail[$yushu];
                            $class = $this->getClassInfoByName($class_detail['class_define_name']);
                            $params['start'] = $class['start'];
                            $params['end'] = $class['end'];
                            $params['class_define_name'] = $class_detail['class_define_name'];

                            $date_str = date('Y-m-d', $timestamp);
                            $where = [
                                'orgnization_id'=>$this->orgnization->id,
                                'user_id'=>$params['user_id'],
                                'date'=>$date_str
                            ];
                            $values = [
                                'class_define_name'=>$params['class_define_name'],
                                'start'=>$params['start'],
                                'end'=>$params['end'],
                                'is_charge'=>$class_group && $class_group->charge_user_id == $params['user_id'] ? 1 : 0,//值长
                                'class_group_name'=>$params['user_class_group'] ? $params['user_class_group']->name : ''
                            ];
                            ClassSchdule::updateOrCreate($where, $values);
                            $index++;
                            $timestamp = $timestamp + 24 * 60 * 60;
                        }
                    }
                    DB::commit();
                } catch (QueryException $e) {
                    DB::rollback();
                }
            }
        }
    }

    //根据班组设置排班
    private function setClassByGroup($params){
        $class_group = ClassGroup::where('name', $params['class_group_name'])->first();
        $user_id_arr = $class_group ? explode(',', $class_group->user_ids) : [];

        DB::beginTransaction();
        try {
            //整个班组成员排班
            if(!empty($user_id_arr)){
                $loop = ClassLoop::find($params['class_loop_id']);
                $loop_detail = $loop ? $loop->detail()->orderBy('sort', 'ASC')->get() : null;
                if($loop && $loop_detail){
                    $loop_detail = $loop_detail->toArray();
                    if($loop_detail && count($loop_detail) > 0){
                        foreach ($user_id_arr as $k8 => $user_id) {
                            //只排一天班
                            if($params['date_type'] == 'single'){
                                $where = [
                                    'orgnization_id'=>$this->orgnization->id,
                                    'user_id'=>$user_id,
                                    'date'=>$params['date']
                                ];
                                $values = [
                                    'class_define_name'=>$params['class_define_name'],
                                    'start'=>$params['start'],
                                    'end'=>$params['end'],
                                    'is_charge'=>$class_group->charge_user_id == $user_id ? 1 : 0,//值长
                                    'class_group_name'=>$params['class_group_name']
                                ];
                                ClassSchdule::updateOrCreate($where, $values);
                            }
                            //周期排班
                            else{
                                $index = 0;
                                if($params['month']){
                                    $temp_timestamp = strtotime($params['date']) + 30 * $params['month'] * 24 * 60 * 60;
                                    $last_date = date('Y-m-t', $temp_timestamp);
                                }
                                else{
                                    $last_date = date('Y-m-t', strtotime($params['date']));
                                }

                                $timestamp = strtotime($params['date']);
                                //循环排班到月底
                                while($timestamp <= strtotime($last_date)){
                                    $yushu = $index%8; //求余
                                    $class_detail = $loop_detail[$yushu];
                                    $class = $this->getClassInfoByName($class_detail['class_define_name']);//班次信息
                                    $params['start'] = $class['start'];
                                    $params['end'] = $class['end'];
                                    $params['class_define_name'] = $class_detail['class_define_name'];

                                    $date_str = date('Y-m-d', $timestamp);
                                    $where = [
                                        'orgnization_id'=>$this->orgnization->id,
                                        'user_id'=>$user_id,
                                        'date'=>$date_str
                                    ];
                                    $values = [
                                        'class_define_name'=>$params['class_define_name'],
                                        'start'=>$params['start'],
                                        'end'=>$params['end'],
                                        'is_charge'=>$class_group->charge_user_id == $user_id ? 1 : 0,//值长
                                        'class_group_name'=>$params['class_group_name']
                                    ];
                                    ClassSchdule::updateOrCreate($where, $values);
                                    $index++;
                                    $timestamp = $timestamp + 24 * 60 * 60;
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
        }
    }

    //获取班次详细信息
    private function getClassInfoByName($class_define_name){
        $start = null;
        $end = null;
        $classes = config('class.cass_define');
        foreach ($classes as $key => $class) {
            if($class_define_name == $class['name'] && $class['time']){
                $time_arr = explode('-', $class['time']);
                $start = $time_arr[0];
                $end = $time_arr[1];
                break;
            }
        }

        return array(
            'start' => $start,
            'end' => $end
        );
    }
}
