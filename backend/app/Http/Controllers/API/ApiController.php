<?php
/**
* 接口权限控制器
*
* @author      cat 叶文华
* @version     1.0 版本号
*/

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use UtilService;
use App\Models\SIS\API;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Http\Requests\API\ApiStoreRequest;
use App\Models\SIS\Orgnization;
use Log;

class ApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/api/tree",
     *     tags={"接口权限apis"},
     *     operationId="apisAll",
     *     summary="接口树",
     *     description="使用说明：获取接口树",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *        )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function tree(){
        $obj = new API();
        $rows = $obj->roots();
        if($rows){
            $arr = [];
            foreach ($rows as $key => $item) {
                $arr[] = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'title' => $item->name,
                    'url' => $item->url,
                    'sort' => $item->sort,
                    'parent_id' => $item->parent_id,
                    'level' => $item->level,
                    'description' => $item->description,
                    'children' => $this->children($item->id)
                );
            }
            $tree = array(
                    array(
                    'id' => 0,
                    'url' => 'wm-mis.com',
                    'sort' => 1,
                    'parent_id' => null,
                    'title' => '全部',
                    'name' => '全部',
                    'level' => 0,
                    'description' => '',
                    'children' => $arr
                )
            );
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $tree);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, []);
        }
    }

    private function children($parent_id){
        $obj = new API();
        $rows = $obj->children($parent_id);
        $arr = [];
        foreach ($rows as $key => $item) {
            $arr[] = array(
                'id' => $item->id,
                'name' => $item->name,
                'title' => $item->name,
                'url' => $item->url,
                'sort' => $item->sort,
                'parent_id' => $item->parent_id,
                'level' => $item->level,
                'description' => $item->description,
                'children' => $this->children($item->id)
            );
        }

        return $arr;
    }

    /**
     * @OA\Post(
     *     path="/api/api/store",
     *     tags={"接口权限apis"},
     *     operationId="storeApi",
     *     summary="保存接口",
     *     description="使用说明：保存接口",
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
     *         description="接口id",
     *         in="query",
     *         name="id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="接口名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="接口描述",
     *         in="query",
     *         name="description",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="接口路径",
     *         in="query",
     *         name="url",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="父接口ID",
     *         in="query",
     *         name="parent_id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="接口方法  POST GET PUT DELETE PATCH",
     *         in="query",
     *         name="method",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"POST", "GET", "PUT", "DELETE", "PATCH"},
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function store(ApiStoreRequest $request){
        $id = $request->input('id');
        $name = $request->input('name');
        $description = $request->input('description');
        $url = $request->input('url');
        $level = 1;
        $parent_id = $request->input('parent_id');
        $sort = $request->input('sort');
        $method = $request->input('method');

        DB::beginTransaction();
        try {
            if ($id) {
                $row = API::find($id);
                $row->name = $name;
                $row->description = $description;
                $row->url = $url;
                $row->parent_id = $parent_id;
                $row->sort = $sort;
                if($method){
                    $row->method = $method;
                }
                $row->save();
            }
            else {
                $params = request(['name', 'description', 'parent_id', 'sort', 'url']);
                if($parent_id){
                    $parent = API::find($parent_id);
                    $level = $parent && $parent->level ? $parent->level + 1 : 1;
                }
                $params['level'] = $level;
                if($method){
                    $params['method'] = $method;
                }
                API::create($params); //save 和 create 的不同之处在于 save 接收整个 Eloquent 模型实例而 create 接收原生 PHP 数组
            }
            DB::commit();
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
        } catch (QueryException $ex) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, $ex->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/api/delete",
     *     tags={"接口权限apis"},
     *     operationId="deleteAPI",
     *     summary="删除用户接口",
     *     description="使用说明：删除用户接口",
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
     *         description="用户接口ID",
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
    public function delete(Request $request){
        $id = $request->input('id');
        $row = API::find($id);
        if($row){
            $children = API::where('parent_id', $id)->get();
            if($children && count($children) > 0 && isset($children[0]->id)){
                return UtilService::format_data(self::AJAX_FAIL, '请先删除子节点', '');
            }

            $row->delete();
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/api/add-tables",
     *     tags={"接口权限apis"},
     *     operationId="addTables",
     *     summary="添加新建电厂数据库表",
     *     description="使用说明：添加新建电厂数据库表",
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
     *         description="电厂简称 英文加数字 如yongqiang1",
     *         in="query",
     *         name="factory",
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
    public function addTables(Request $request){
        $factory = $request->input('factory');
        if(!$this->validate_factory($factory)){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }

        $base_path = base_path();
        $des_file = $base_path . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . date('Y_m_d') . '_' . rand(100000, 999999) . '_create_factory_table.php';
        $tpl_file = $base_path . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . '2022_11_01_131620_create_factory_table.php';

        try{
            copy($tpl_file, $des_file);
            $contents = file_get_contents($des_file);
            $new_content = str_replace('yongqiang2', $factory, $contents);
            file_put_contents($des_file, $new_content);
            Artisan::queue('migrate', []);
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $des_file);
        } catch (Exception $e) {
            if(file_exists($des_file)){
                unlink($des_file);
            }
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    private function validate_factory($factory){
        $tb_list = [];
        $datalist = Orgnization::where('level', 2)->get();
        foreach ($datalist as $key => $item) {
            $tb_list[] = $item->code;
        }
        if(!$factory || ($factory && !in_array($factory, $tb_list))){
            return false;
        }
        else{
            return true;
        }
    }
}
