<?php

namespace App\Http\Controllers\MIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use UtilService;
use App\Models\MIS\DevicePropertyTemplate;

class DevicePropertyTemplateController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/device-property-template/page",
     *     tags={"设备属性模板device-property-template"},
     *     operationId="device-property-template-index",
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
     *                  @OA\Items(ref="#/components/schemas/DevicePropertyTemplate")
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
        $rows = DevicePropertyTemplate::select(['*']);

        if ($name) {
            $rows = $rows->where('name', 'like', "%{$name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $rows, 'total' => $total]);
    }

    /**
     * @OA\Get(
     *     path="/api/device-property-template/tree",
     *     tags={"设备属性模板device-property-template"},
     *     operationId="device-property-template tree",
     *     summary="设备属性模板树",
     *     description="使用说明：设备属性模板树",
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
     *         description="最大层级",
     *         in="query",
     *         name="level",
     *         required=false,
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
    public function tree(Request $request){
        $level = $request->input('level');
        $obj = new DevicePropertyTemplate();
        $rows = $obj->roots();
        if($rows){
            $arr = [];
            foreach ($rows as $key => $item) {
                $arr[] = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'title' => $item->name,
                    'type' => $item->type,
                    'sort' => $item->sort,
                    'parent_id' => $item->parent_id,
                    'level' => $item->level,
                    'is_group' => $item->is_group,
                    'children' => $this->children($item->id, $level)
                );
            }
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $arr);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', []);
        }
    }

    private function children($parent_id, &$level=''){
        $obj = new DevicePropertyTemplate();
        $rows = $obj->children($parent_id);
        $arr = [];
        foreach ($rows as $key => $item) {
            if(!$level || $item->level < $level){
                $children =  $this->children($item->id, $level);
            }
            else{
                $children = [];
            }
            $arr[] = array(
                'id' => $item->id,
                'name' => $item->name,
                'title' => $item->name,
                'type' => $item->type,
                'sort' => $item->sort,
                'parent_id' => $item->parent_id,
                'level' => $item->level,
                'is_group' => $item->is_group,
                'children' => $children
            );
        }

        return $arr;
    }

    /**
     * @OA\Post(
     *     path="/api/device-property-template/store",
     *     tags={"设备属性模板device-property-template"},
     *     operationId="storeDevicePropertyTemplate",
     *     summary="保存设备属性模板",
     *     description="使用说明：保存设备属性模板",
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
     *         description="设备属性模板id",
     *         in="query",
     *         name="id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="属性名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="模板类型 text文本, integer数字, image图片, date日期, radio单选, checkbox多选",
     *         in="query",
     *         name="type",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             default="text",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"text", "integer", "image", "date", "radio", "checkbox"},
     *             )
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="父主键ID",
     *         in="query",
     *         required=false,
     *         name="parent_id",
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="排序号",
     *         in="query",
     *         name="sort",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="是否文件夹",
     *         in="query",
     *         name="is_group",
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
    public function store(Request $request){
        $id = $request->input('id');
        $name = $request->input('name');
        $type = $request->input('type');
        $level = 1;
        $parent_id = $request->input('parent_id');
        $sort = $request->input('sort');
        $is_group = $request->input('is_group');

        DB::beginTransaction();
        try {
            $parent = null;
            if($parent_id){
                $parent = DevicePropertyTemplate::find($parent_id);
            }

            if ($id) {
                $row = DevicePropertyTemplate::find($id);
                $row->name = $name;
                $row->type = $type;
                $row->parent_id = $parent_id;
                $row->sort = $sort;
                $row->is_group = $is_group;
                if($parent){
                    $row->ancestor_id = $parent->ancestor_id;
                }
                $row->save();
            }
            else {
                $params = request(['name', 'type', 'parent_id', 'sort', 'is_group']);
                $level = $parent && $parent->level ? $parent->level + 1 : 1;
                $params['level'] = $level;
                if($parent){
                    $params['ancestor_id'] = $parent->ancestor_id;
                }
                $row = DevicePropertyTemplate::create($params); //save 和 create 的不同之处在于 save 接收整个 Eloquent 模型实例而 create 接收原生 PHP 数组
                if(!$parent){
                    //没有父设备  祖先ID为自己的ID
                    $row->ancestor_id = $row->id;
                    $row->save();
                }
            }
            DB::commit();
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', '');
        } catch (QueryException $ex) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', $ex->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/device-property-template/{id}",
     *     tags={"设备属性模板device-property-template"},
     *     operationId="device-property-template-show",
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
     *         description="DevicePropertyTemplate主键",
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
     *                      @OA\Schema(ref="#/components/schemas/DevicePropertyTemplate"),
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
        $row = DevicePropertyTemplate::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, '该数据不存在', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $row);
    }

    /**
     * @OA\Delete(
     *     path="/api/device-property-template/{id}",
     *     tags={"设备属性模板device-property-template"},
     *     operationId="device-property-template-destroy",
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
     *         description="DevicePropertyTemplate主键",
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
        $row = DevicePropertyTemplate::find($id);
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
