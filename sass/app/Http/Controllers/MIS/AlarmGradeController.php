<?php

namespace App\Http\Controllers\MIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use UtilService;
use App\Models\Mis\AlarmGrade;

class AlarmGradeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/alarm-grade",
     *     tags={"报警等级alarm-grade"},
     *     operationId="alarm-grade-index",
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
     *         @OA\JsonContent(
     *             @OA\Property(
	 *              	property="code",
	 *                  description="错误代码，0：为没有错误",
	 *                  type="integer",
	 *					default="0"
	 *             ),
     *             @OA\Property(
	 *                  property="data",
	 *                  description="返回数据",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/AlarmGrade")
     *             ),
     *             @OA\Property(
	 *              	property="message",
	 *                  description="错误消息",
	 *                  type="string"
	 *             )
     *         ),
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
        $rows = AlarmGrade::select(['*']);

        if ($name) {
            $rows = $rows->where('name', 'like', "%{$name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $rows, 'total' => $total]);
    }

    /**
     * @OA\Post(
     *     path="/api/alarm-grade",
     *     tags={"报警等级alarm-grade"},
     *     operationId="alarm-grade-store",
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
     *         description="中文名字",
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
     *     @OA\Parameter(
     *         description="最小值",
     *         in="query",
     *         name="min_value",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="最大值",
     *         in="query",
     *         name="max_value",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *         @OA\JsonContent(
     *             @OA\Property(
	 *              	property="code",
	 *                  description="错误代码，0：为没有错误",
	 *                  type="integer",
	 *					default="0"
	 *             ),
     *             @OA\Property(
	 *                  property="data",
	 *                  description="返回数据",
     *                  type="object",
     *                  allOf={
     *                      @OA\Schema(ref="#/components/schemas/AlarmGrade"),
     *                  }
     *             ),
     *             @OA\Property(
	 *              	property="message",
	 *                  description="错误消息",
	 *                  type="string"
	 *             )
     *         ),
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $input = $request->only(['name', 'description', 'min_value', 'max_value']);
        //判断是否有其他相同的名称
        $data = AlarmGrade::where('name', $input['name'])->first();
        if($data && $data->name){
            return UtilService::format_data(self::AJAX_FAIL, '中文名称已存在', '');
        }

        try {
            $res = AlarmGrade::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
    }

    /**
     * @OA\Get(
     *     path="/api/alarm-grade/{id}",
     *     tags={"报警等级alarm-grade"},
     *     operationId="alarm-grade-show",
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
     *         description="AlarmGrade主键",
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
     *         @OA\JsonContent(
     *             @OA\Property(
	 *              	property="code",
	 *                  description="错误代码，0：为没有错误",
	 *                  type="integer",
	 *					default="0"
	 *             ),
     *             @OA\Property(
	 *                  property="data",
	 *                  description="返回数据",
     *                  type="object",
     *                  allOf={
     *                      @OA\Schema(ref="#/components/schemas/AlarmGrade"),
     *                  }
     *             ),
     *             @OA\Property(
	 *              	property="message",
	 *                  description="错误消息",
	 *                  type="string"
	 *             )
     *         ),
     *     ),
     * )
     */
    public function show($id)
    {
        $row = AlarmGrade::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, '该数据不存在', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $row);
    }

    /**
     * @OA\Put(
     *     path="/api/alarm-grade/{id}",
     *     tags={"报警等级alarm-grade"},
     *     operationId="alarm-grade-update",
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
     *         description="AlarmGrade主键",
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
     *         description="描述",
     *         in="query",
     *         name="description",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="最小值",
     *         in="query",
     *         name="min_value",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="最大值",
     *         in="query",
     *         name="max_value",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="update succeed",
     *         @OA\JsonContent(
     *             @OA\Property(
	 *              	property="code",
	 *                  description="错误代码，0：为没有错误",
	 *                  type="integer",
	 *					default="0"
	 *             ),
     *             @OA\Property(
	 *                  property="data",
	 *                  description="返回数据",
     *                  type="object",
     *                  allOf={
     *                      @OA\Schema(ref="#/components/schemas/AlarmGrade"),
     *                  }
     *             ),
     *             @OA\Property(
	 *              	property="message",
	 *                  description="错误消息",
	 *                  type="string"
	 *             )
     *         ),
     *     ),
     * )
     */
    public function update(Request $request, $id)
    {
        $row = AlarmGrade::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该数据不存在', ''));
        }
        $input = $request->input();

        //判断是否有其他相同的名称
        $data = AlarmGrade::where('name', $input['name'])->first();
        if($data && $data->id != $id){
            return UtilService::format_data(self::AJAX_FAIL, '中文名称已存在', '');
        }

        $allowField = ['name', 'description', 'min_value', 'max_value'];
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
     *     path="/api/alarm-grade/{id}",
     *     tags={"报警等级alarm-grade"},
     *     operationId="alarm-grade-destroy",
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
     *         description="AlarmGrade主键",
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
        $row = AlarmGrade::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, '该数据不存在', '');
        }
        try {
            $row->delete();
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, '删除失败', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '删除成功', '');
    }
}
