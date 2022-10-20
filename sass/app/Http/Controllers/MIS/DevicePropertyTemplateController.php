<?php

namespace App\Http\Controllers\MIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use UtilService;
use App\Models\MIS\DevicePropertyTemplate;
use App\Models\MIS\DeviceTemplate;

class DevicePropertyTemplateController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/device-property-template/lists",
     *     tags={"设备属性模板device-property-template"},
     *     operationId="device-property-template-lists",
     *     summary="获取所有数据列表",
     *     description="使用说明：获取所有数据列表",
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
     *         description="名称",
     *         in="query",
     *         name="name",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function lists(Request $request)
    {
        $name = $request->input('name');
        if ($name) {
            $rows = DeviceTemplate::where('name', 'like', "%{$name}%")->where('orgnization_id', $this->orgnization->id)->get();
        }
        else{
            $rows = DeviceTemplate::where('orgnization_id', $this->orgnization->id)->get();
        }

        foreach ($rows as $key => $item) {
            $properties = $item->device_property_templates;
            foreach ($properties as $k2 => $property) {
                $property_tpl_obj = DevicePropertyTemplate::find($id);
                $properties[$k2]['property_name'] = $property_tpl_obj && $property_tpl_obj->name ? $property_tpl_obj->name : '';
                $properties[$k2]['property_value'] = $property->value;
            }
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $rows);
    }

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
     *                  @OA\Items(ref="#/components/schemas/DeviceTemplate")
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
        $rows = DeviceTemplate::select(['*'])->where('orgnization_id', $this->orgnization->id);

        if ($name) {
            $rows = $rows->where('name', 'like', "%{$name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $properties = $item->device_property_templates;
            foreach ($properties as $k2 => $property) {
                $property_tpl_obj = DevicePropertyTemplate::find($property->id);
                $properties[$k2]['property_name'] = $property_tpl_obj && $property_tpl_obj->name ? $property_tpl_obj->name : '';
                $properties[$k2]['property_value'] = $property->value;
            }
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total]);
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
        $obj = new DeviceTemplate();
        $rows = $obj->roots($this->orgnization->id);
        if($rows){
            $arr = [];
            foreach ($rows as $key => $item) {
                $properties = $item->device_property_templates;
                foreach ($properties as $k2 => $property) {
                    $property_tpl_obj = DevicePropertyTemplate::find($property->id);
                    $properties[$k2]['property_name'] = $property_tpl_obj && $property_tpl_obj->name ? $property_tpl_obj->name : '';
                    $properties[$k2]['property_value'] = $property->value;
                }

                $arr[] = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'title' => $item->name,
                    'sort' => $item->sort,
                    'parent_id' => $item->parent_id,
                    'level' => $item->level,
                    'is_group' => $item->is_group,
                    'children' => $this->children($item->id, $level)
                );
            }
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $arr);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', []);
        }
    }

    private function children($parent_id, &$level=''){
        $obj = new DeviceTemplate();
        $rows = $obj->children($parent_id);
        $arr = [];
        foreach ($rows as $key => $item) {
            $properties = $item->device_property_templates;
            foreach ($properties as $k2 => $property) {
                $property_tpl_obj = DevicePropertyTemplate::find($property->id);
                $properties[$k2]['property_name'] = $property_tpl_obj && $property_tpl_obj->name ? $property_tpl_obj->name : '';
                $properties[$k2]['property_value'] = $property->value;
            }

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
     *     operationId="storeDeviceTemplate",
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
     *         description="设备模板id",
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
     *     @OA\Parameter(
     *         description="自定义属性json格式  [{'name': '属性名', 'type': '属性类型', 'value': '属性值', 'default_value': '默认属性'}]  类型text文本, integer数字, image图片, date日期, radio单选框, checkbox复选框, select下拉列表, switch开关",
     *         in="query",
     *         name="properties",
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
        $level = 1;
        $parent_id = $request->input('parent_id');
        $sort = $request->input('sort');
        $is_group = $request->input('is_group');
        $properties = $request->input('properties');
        $properties = json_decode($properties, true);

        DB::beginTransaction();
        try {
            $parent = null;
            if($parent_id){
                $parent = DeviceTemplate::find($parent_id);
            }

            if ($id) {
                $row = DeviceTemplate::find($id);
                if($row && $row->orgnization_id != $this->orgnization->id){
                    return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
                }
                elseif($row){
                    $row->device_property_templates()->forceDelete();
                }

                $row->name = $name;
                $row->parent_id = $parent_id;
                $row->sort = $sort;
                $row->is_group = $is_group;
                if($parent){
                    $row->ancestor_id = $parent->ancestor_id;
                }
                $row->save();

                //保存设备属性模板
                if($properties && !empty($properties) && count($properties) > 0){
                    foreach ($properties as $key => $property) {
                        if(isset($property['name']) && isset($property['type']) && isset($property['value']) && isset($property['default_value'])){
                            DevicePropertyTemplate::create([
                                'device_template_id' => $id,
                                'type' => $property['type'],
                                'name' => $property['name'],
                                'value' => $property['value'],
                                'default_value' => $property['default_value']
                            ]);
                        }
                    }
                }
            }
            else {
                $params = request(['name', 'parent_id', 'sort', 'is_group']);
                $level = $parent && $parent->level ? $parent->level + 1 : 1;
                $params['level'] = $level;
                $params['orgnization_id'] = $this->orgnization->id;
                if($parent){
                    $params['ancestor_id'] = $parent->ancestor_id;
                }
                $row = DeviceTemplate::create($params); //save 和 create 的不同之处在于 save 接收整个 Eloquent 模型实例而 create 接收原生 PHP 数组
                if(!$parent){
                    //没有父设备  祖先ID为自己的ID
                    $row->ancestor_id = $row->id;
                    $row->save();
                }

                //保存设备属性模板
                if($properties && !empty($properties) && count($properties) > 0){
                    foreach ($properties as $key => $property) {
                        if(isset($property['name']) && isset($property['type']) && isset($property['value']) && isset($property['default_value'])){
                            DevicePropertyTemplate::create([
                                'device_template_id' => $row->id,
                                'type' => $property['type'],
                                'name' => $property['name'],
                                'value' => $property['value'],
                                'default_value' => $property['default_value'],
                                'orgnization_id' => $this->orgnization->id
                            ]);
                        }
                    }
                }
            }
            DB::commit();
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
        } catch (QueryException $ex) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, $ex->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/device-property-template/show/{id}",
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
     *         description="DeviceTemplate主键",
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
     *                      @OA\Schema(ref="#/components/schemas/DeviceTemplate"),
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
        $row = DeviceTemplate::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, '该数据不存在', '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        $properties = $row->device_property_templates;
        foreach ($properties as $k2 => $property) {
            $property_tpl_obj = DevicePropertyTemplate::find($property->id);
            $properties[$k2]['property_name'] = $property_tpl_obj && $property_tpl_obj->name ? $property_tpl_obj->name : '';
            $properties[$k2]['property_value'] = $property->value;
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Post(
     *     path="/api/device-property-template/destroy/{id}",
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
     *         description="DeviceTemplate主键",
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
        $row = DeviceTemplate::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, '该数据不存在', '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        try {
            $row->delete();
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, '删除失败', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '删除成功', '');
    }
}
