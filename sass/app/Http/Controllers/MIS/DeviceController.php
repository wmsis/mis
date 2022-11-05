<?php

namespace App\Http\Controllers\MIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UtilService;
use App\Models\MIS\Device;
use App\Models\MIS\DeviceProperty;
use App\Models\MIS\DevicePropertyTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\MIS\InspectRule;
use Log;

class DeviceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/device/lists",
     *     tags={"设备档案device"},
     *     operationId="device-lists",
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
     *     @OA\Parameter(
     *         description="是否文件夹",
     *         in="query",
     *         name="is_group",
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
     *                  @OA\Items(ref="#/components/schemas/Device")
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
    public function lists(Request $request)
    {
        $name = $request->input('name');
        $is_group = $request->input('is_group');

        $rows = Device::select(['*'])->where('orgnization_id', $this->orgnization->id);
        if ($name) {
            $rows = $rows->where('name', 'like', "%{$name}%");
        }

        if (isset($is_group)) {
            $rows = $rows->where('is_group', $is_group);
        }

        $rows = $rows->get();
        foreach ($rows as $key => $item) {
            $properties = $item->device_properties;
            foreach ($properties as $k2 => $property) {
                $property_tpl_obj = DevicePropertyTemplate::find($property->device_property_template_id);
                $properties[$k2]['property_name'] = $property_tpl_obj && $property_tpl_obj->name ? $property_tpl_obj->name : '';
                $properties[$k2]['property_value'] = $property->value;
                $properties[$k2]['values'] = $property_tpl_obj && $property_tpl_obj->value ? $property_tpl_obj->value : '';
                $properties[$k2]['type'] = $property_tpl_obj && $property_tpl_obj->type ? $property_tpl_obj->type : '';
                $properties[$k2]['default_value'] = $property_tpl_obj && $property_tpl_obj->default_value ? $property_tpl_obj->default_value : '';
                $inspect_rule = $property->inspect_rule;
            }
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $rows);
    }

    /**
     * @OA\Get(
     *     path="/api/device/page",
     *     tags={"设备档案device"},
     *     operationId="device-index",
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
     *         description="是否文件夹",
     *         in="query",
     *         name="is_group",
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
     *                  @OA\Items(ref="#/components/schemas/Device")
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
        $is_group = $request->input('is_group');
        $rows = Device::select(['*'])->where('orgnization_id', $this->orgnization->id);

        if ($name) {
            $rows = $rows->where('name', 'like', "%{$name}%");
        }

        if (isset($is_group)) {
            $rows = $rows->where('is_group', $is_group);
        }

        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $properties = $item->device_properties;
            foreach ($properties as $k2 => $property) {
                $property_tpl_obj = DevicePropertyTemplate::find($property->device_property_template_id);
                $properties[$k2]['property_name'] = $property_tpl_obj && $property_tpl_obj->name ? $property_tpl_obj->name : '';
                $properties[$k2]['property_value'] = $property->value;
                $properties[$k2]['values'] = $property_tpl_obj && $property_tpl_obj->value ? $property_tpl_obj->value : '';
                $properties[$k2]['type'] = $property_tpl_obj && $property_tpl_obj->type ? $property_tpl_obj->type : '';
                $properties[$k2]['default_value'] = $property_tpl_obj && $property_tpl_obj->default_value ? $property_tpl_obj->default_value : '';
                $inspect_rule = $property->inspect_rule;
            }
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Get(
     *     path="/api/device/tree",
     *     tags={"设备档案device"},
     *     operationId="device tree",
     *     summary="设备树",
     *     description="使用说明：获取设备树",
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
        $obj = new Device();
        $rows = $obj->roots($this->orgnization->id);
        if($rows){
            $arr = [];
            foreach ($rows as $key => $item) {
                $properties = $item->device_properties;
                foreach ($properties as $k2 => $property) {
                    $property_tpl_obj = DevicePropertyTemplate::find($property->device_property_template_id);
                    $properties[$k2]['property_name'] = $property_tpl_obj && $property_tpl_obj->name ? $property_tpl_obj->name : '';
                    $properties[$k2]['property_value'] = $property->value;
                    $properties[$k2]['values'] = $property_tpl_obj && $property_tpl_obj->value ? $property_tpl_obj->value : '';
                    $properties[$k2]['type'] = $property_tpl_obj && $property_tpl_obj->type ? $property_tpl_obj->type : '';
                    $properties[$k2]['default_value'] = $property_tpl_obj && $property_tpl_obj->default_value ? $property_tpl_obj->default_value : '';
                    $inspect_rule = $property->inspect_rule;
                }
                $arr[] = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'title' => $item->name,
                    'code' => $item->code,
                    'sort' => $item->sort,
                    'parent_id' => $item->parent_id,
                    'level' => $item->level,
                    'quality_date' => $item->quality_date,
                    'factory_date' => $item->factory_date,
                    'img' => $item->img,
                    'properties' => $properties,
                    'is_group' => $item->is_group,
                    'device_template_id' => $item->device_template_id,
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
        $obj = new Device();
        $rows = $obj->children($parent_id);
        $arr = [];
        foreach ($rows as $key => $item) {
            $properties = $item->device_properties;
            foreach ($properties as $k2 => $property) {
                $property_tpl_obj = DevicePropertyTemplate::find($property->device_property_template_id);
                $properties[$k2]['property_name'] = $property_tpl_obj && $property_tpl_obj->name ? $property_tpl_obj->name : '';
                $properties[$k2]['property_value'] = $property->value;
                $properties[$k2]['values'] = $property_tpl_obj && $property_tpl_obj->value ? $property_tpl_obj->value : '';
                $properties[$k2]['type'] = $property_tpl_obj && $property_tpl_obj->type ? $property_tpl_obj->type : '';
                $properties[$k2]['default_value'] = $property_tpl_obj && $property_tpl_obj->default_value ? $property_tpl_obj->default_value : '';
                $inspect_rule = $property->inspect_rule;
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
                'code' => $item->code,
                'sort' => $item->sort,
                'parent_id' => $item->parent_id,
                'level' => $item->level,
                'quality_date' => $item->quality_date,
                'factory_date' => $item->factory_date,
                'img' => $item->img,
                'properties' => $properties,
                'is_group' => $item->is_group,
                'device_template_id' => $item->device_template_id,
                'children' => $children
            );
        }

        return $arr;
    }

    /**
     * @OA\Post(
     *     path="/api/device/store",
     *     tags={"设备档案device"},
     *     operationId="storeDevice",
     *     summary="保存设备",
     *     description="使用说明：保存设备",
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
     *         description="设备id",
     *         in="query",
     *         name="id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="设备名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="设备编码",
     *         in="query",
     *         name="code",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="质检日期",
     *         in="query",
     *         name="quality_date",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="出厂日期",
     *         in="query",
     *         name="factory_date",
     *         required=false,
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
     *         description="图片",
     *         in="query",
     *         name="img",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
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
     *         description="设备模板ID",
     *         in="query",
     *         name="device_template_id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="自定义属性json格式  [{'device_property_template_id': '属性模板ID', 'value': '属性名'}]",
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
        $code = $request->input('code');
        $quality_date = $request->input('quality_date');
        $factory_date = $request->input('factory_date');
        $level = 1;
        $parent_id = $request->input('parent_id');
        $sort = $request->input('sort');
        $img = $request->input('img');
        $is_group = $request->input('is_group');
        $properties = $request->input('properties');
        $properties = json_decode($properties, true);
        $device_template_id = $request->input('device_template_id');

        DB::beginTransaction();
        try {
            $parent = null;
            if($parent_id){
                $parent = Device::find($parent_id);
            }

            if ($id) {
                $row = Device::find($id);
                if($row && $row->orgnization_id != $this->orgnization->id){
                    return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
                }

                $row->name = $name;
                $row->code = $code;
                $row->factory_date = $factory_date;
                $row->quality_date = $quality_date;
                $row->parent_id = $parent_id;
                $row->img = $img;
                $row->sort = $sort;
                $row->is_group = $is_group;
                if($device_template_id){
                    $row->device_template_id = $device_template_id;
                }

                if($parent){
                    $row->ancestor_id = $parent->ancestor_id;
                }
                $row->save();

                //自定义属性
                $row->device_properties()->forceDelete();  //先删除以前的属性
                if($properties && !empty($properties) && count($properties) > 0){
                    foreach ($properties as $key => $property) {
                        if(isset($property['device_property_template_id']) && isset($property['value'])){
                            DeviceProperty::create([
                                'device_id' => $id,
                                'device_property_template_id' => $property['device_property_template_id'],
                                'value' => $property['value']
                            ]);
                        }
                    }
                }
            }
            else {
                $params = request(['name', 'code', 'factory_date', 'quality_date', 'parent_id', 'sort', 'img', 'is_group']);
                $level = $parent && $parent->level ? $parent->level + 1 : 1;
                $params['level'] = $level;
                $params['orgnization_id'] = $this->orgnization->id;
                if($parent){
                    $params['ancestor_id'] = $parent->ancestor_id;
                }
                if($device_template_id){
                    $params['device_template_id'] = $device_template_id;
                }

                $row = Device::create($params); //save 和 create 的不同之处在于 save 接收整个 Eloquent 模型实例而 create 接收原生 PHP 数组
                if(!$parent){
                    //没有父设备  祖先ID为自己的ID
                    $row->ancestor_id = $row->id;
                    $row->save();
                }

                if($properties && !empty($properties) && count($properties) > 0){
                    foreach ($properties as $key => $property) {
                        if(isset($property['device_property_template_id']) && isset($property['value'])){
                            DeviceProperty::create([
                                'device_id' => $row->id,
                                'device_property_template_id' => $property['device_property_template_id'],
                                'value' => $property['value']
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
     *     path="/api/device/show/{id}",
     *     tags={"设备档案device"},
     *     operationId="device-show",
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
     *         description="设备主键",
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
     *                      @OA\Schema(ref="#/components/schemas/Device"),
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
        $row = Device::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        $properties = $row->device_properties;
        foreach ($properties as $key => $property) {
            $property_tpl_obj = DevicePropertyTemplate::find($property->device_property_template_id);
            $properties[$key]['property_name'] = $property_tpl_obj && $property_tpl_obj->name ? $property_tpl_obj->name : '';
            $properties[$key]['property_value'] = $property->value;
            $properties[$key]['values'] = $property_tpl_obj && $property_tpl_obj->value ? $property_tpl_obj->value : '';
            $properties[$key]['type'] = $property_tpl_obj && $property_tpl_obj->type ? $property_tpl_obj->type : '';
            $properties[$key]['default_value'] = $property_tpl_obj && $property_tpl_obj->default_value ? $property_tpl_obj->default_value : '';
            $inspect_rule = $property->inspect_rule;
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Post(
     *     path="/api/device/destroy/{id}",
     *     tags={"设备档案device"},
     *     operationId="device-destroy",
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
     *         description="设备主键",
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
        $row = Device::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        elseif($row && $row->orgnization_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        $children = Device::where('parent_id', $id)->get();
        if($children && count($children) > 0 && isset($children[0]->id)){
            return UtilService::format_data(self::AJAX_FAIL, '请先删除子节点', '');
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
     *     path="/api/device/upload",
     *     tags={"设备档案device"},
     *     operationId="device-upload",
     *     summary="上传文件",
     *     description="使用说明：上传文件",
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
     *         description="上传文件名",
     *         in="query",
     *         name="file",
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
    public function upload(Request $request)
    {
        $file = $request->file('file');
        $name = $this->orgnization->code.'/device/'.date('Ymd');  //指定的是目录名，而不是文件名 storage/app下
        $path = $file->store($name, 'uploads');    //返回文件的路径和文件名，第二个参数为指定磁盘
        $path = '/uploads/'.$path;

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, [
            "path" => $path
        ]);
    }
}
