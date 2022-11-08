<?php
/**
* 租户组织控制器
*
* @author      cat 叶文华
* @version     1.0 版本号
*/

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UtilService;
use MyCacheService;
use App\Models\SIS\Orgnization;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Http\Requests\User\StoreRoleRequest;
use App\Models\Role;
use App\Models\User;
use Log;

class OrgnizationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orgnizations/page",
     *     tags={"公司组织架构orgnizations"},
     *     operationId="orgnizations-page",
     *     summary="分页获取电厂数据列表",
     *     description="使用说明：分页获取电厂数据列表",
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
     *         description="每页数据量",
     *         in="query",
     *         name="num",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=20,
     *         ),
     *      ),
     *      @OA\Parameter(
     *          description="页数",
     *          in="query",
     *          name="page",
     *          @OA\Schema(
     *             type="integer",
     *             default=1,
     *          ),
     *          required=false,
     *      ),
     *      @OA\Parameter(
     *          description="中文名称搜索",
     *          in="query",
     *          name="name",
     *          required=false,
     *          @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="succeed",
     *          @OA\Schema(
     *               @OA\Property(
     *                   property="Orgnizations",
     *                   description="Orgnizations",
     *                   allOf={
     *                       @OA\Schema(ref="#/definitions/Orgnization")
     *                   }
     *                )
     *           )
     *      ),
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;
        $name = $request->input('name');
        $obj = new Orgnization();

        $rows = $obj->select(['*'])->where('level', 2);
        if ($name) {
            $rows = $rows->where('name', 'like', "%{$name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key=>$item) {
            if($item->ancestor_id){
                $ancestor = Orgnization::find($item->ancestor_id);
                $rows[$key]->ancestor_name = $ancestor ? $ancestor->name : '';
            }
            else{
                $rows[$key]->ancestor_name = '';
            }
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Post(
     *     path="/api/orgnizations/switch",
     *     tags={"公司组织架构orgnizations"},
     *     operationId="orgnizations-switch",
     *     summary="切换组织",
     *     description="使用说明：切换组织",
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
     *         description="组织ID",
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
        $user = auth('api')->user();
        if($user->type != 'admin'){
            $data = $user->orgnizations()->where('orgnization_id', $id)->first();
        }
        else{
            $data = Orgnization::where('id', $id)->first();
        }

        if($data){
            $server = $request->server();
            $domain = $server['HTTP_HOST'];
            $third = UtilService::third_domain($domain);
            $data = $data->toArray();
            $user = auth('api')->user();
            if($user){
                $user->last_login_orgnization = $id;
                $user->save();
                $key = UtilService::getKey($user->id, 'ORGNIZATION' . $third);
                $expire = auth('api')->factory()->getTTL() * 60;
                MyCacheService::setCache($key, $data, $expire);
            }
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $data);
        }
        return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
    }

    /**
     * @OA\Get(
     *     path="/api/orgnizations/factories",
     *     tags={"公司组织架构orgnizations"},
     *     operationId="orgnizations-factories",
     *     summary="获取所有电厂列表",
     *     description="使用说明：获取所有电厂列表",
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
     *                  property="Orgnizations",
     *                  description="Orgnizations",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/Orgnizations")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function factories(Request $request)
    {
        $data = Orgnization::where('level', 2)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $data);
    }

    /**
     * @OA\Get(
     *     path="/api/orgnizations/tree",
     *     tags={"公司组织架构orgnizations"},
     *     operationId="orgnizationsAll",
     *     summary="组织树",
     *     description="使用说明：获取组织树",
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
    public function tree(Request $request){
        $level = $request->input('level');
        $org = new Orgnization();
        $rows = $org->roots();
        if($rows){
            $arr = [];
            foreach ($rows as $key => $item) {
                $arr[] = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'title' => $item->name,
                    'sub_title' => $item->sub_title,
                    'code' => $item->code,
                    'sort' => $item->sort,
                    'parent_id' => $item->parent_id,
                    'level' => $item->level,
                    'description' => $item->description,
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
        $org = new Orgnization();
        $rows = $org->children($parent_id);
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
                'sub_title' => $item->sub_title,
                'code' => $item->code,
                'sort' => $item->sort,
                'parent_id' => $item->parent_id,
                'level' => $item->level,
                'description' => $item->description,
                'children' => $children
            );
        }

        return $arr;
    }

    /**
     * @OA\Post(
     *     path="/api/orgnizations/store",
     *     tags={"公司组织架构orgnizations"},
     *     operationId="storeOrgnization",
     *     summary="保存组织",
     *     description="使用说明：保存组织",
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
     *         description="组织id",
     *         in="query",
     *         name="id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="组织名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="组织编码(电厂英文全拼+数字)",
     *         in="query",
     *         name="code",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="组织描述",
     *         in="query",
     *         name="description",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="父组织ID",
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
    public function store(Request $request){
        $id = $request->input('id');
        $name = $request->input('name');
        $code = $request->input('code');
        $sub_title = $request->input('sub_title');
        $description = $request->input('description');
        $level = 1;
        $parent_id = $request->input('parent_id');
        $sort = $request->input('sort');

        DB::beginTransaction();
        try {
            $parent = null;
            if($parent_id){
                $parent = Orgnization::find($parent_id);
            }

            if ($id) {
                $row = Orgnization::find($id);
                if($row && $row->ancestor_id != $this->orgnization->id){
                    return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
                }

                $row->name = $name;
                $row->code = $code;
                $row->description = $description;
                $row->sub_title = $sub_title;
                $row->parent_id = $parent_id;
                $row->sort = $sort;
                if($parent){
                    if($parent->level == 1){
                        //如果父组织是一级组织，祖先ID就为该组织ID
                        $ancestor_id = $id;
                    }
                    else{
                        //如果父组织是大于等于二级组织，则祖先ID和父组织的祖先ID相同
                        $ancestor_id = $parent->ancestor_id;
                    }

                    $row->ancestor_id = $ancestor_id;
                }
                $row->save();
            }
            else {
                $params = request(['name', 'code', 'description', 'sub_title', 'parent_id', 'sort']);
                $level = $parent && $parent->level ? $parent->level + 1 : 1;
                $params['level'] = $level;
                $row = Orgnization::create($params); //save 和 create 的不同之处在于 save 接收整个 Eloquent 模型实例而 create 接收原生 PHP 数组
                if($parent){
                    if($parent->level == 1){
                        //如果父组织是一级组织，祖先ID就为该组织（二级）ID 一级组织没有ancestor_id 二级组织的祖先ID为自己的ID
                        $ancestor_id = $row->id;
                    }
                    else{
                        //如果父组织是大于等于二级组织，则祖先ID和父组织的祖先ID相同
                        $ancestor_id = $parent->ancestor_id;
                    }
                    $row->ancestor_id = $ancestor_id;
                    $row->save();
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
     *     path="/api/orgnizations/{orgnization}/role",
     *     tags={"公司组织架构orgnizations"},
     *     operationId="getOrgRole",
     *     summary="组织角色",
     *     description="使用说明：获取组织角色",
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
     *         description="用户组织ID",
     *         in="path",
     *         name="id",
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
    public function role(Orgnization $orgnization){
        $roles = Role::where('orgnization_id', $this->orgnization->id)->get(); // all roles
        $myRoles = $orgnization->roles; //带括号的是返回关联对象实例，不带括号是返回动态属性

        //compact 创建一个包含变量名和它们的值的数组
        $data = compact('roles', 'myRoles');
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $data);
    }

    /**
     * @OA\Post(
     *     path="/api/orgnizations/{orgnization}/role",
     *     tags={"公司组织架构orgnizations"},
     *     operationId="storeOrgRole",
     *     summary="保存组织角色",
     *     description="使用说明：保存组织角色",
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
     *         description="用户组织ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="角色ID列表",
     *         in="query",
     *         name="roles",
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
    public function storeRole(StoreRoleRequest $request, Orgnization $orgnization){
        if($orgnization->ancestor_id != $this->orgnization->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }

        //验证
        $param_arr = explode(',', request('roles'));
        $roles = Role::whereIn('id', $param_arr)->get();
        $myRoles = $orgnization->roles;

        //要增加的角色
        $addRoles = $roles->diff($myRoles);
        foreach ($addRoles as $role) {
            $orgnization->assignRole($role);
        }

        //要删除的角色
        $deleteRoles = $myRoles->diff($roles);
        foreach ($deleteRoles as $role) {
            $orgnization->deleteRole($role);
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, []);
    }

    /**
     * @OA\Post(
     *     path="/api/orgnizations/delete",
     *     tags={"公司组织架构orgnizations"},
     *     operationId="deleteOrgnization",
     *     summary="删除组织",
     *     description="使用说明：删除组织",
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
     *         description="用户组织ID",
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
        $row = Orgnization::find($id);
        if($row){
            if($row->ancestor_id != $this->orgnization->id){
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
            }

            $children = Orgnization::where('parent_id', $id)->get();
            if($children && count($children) > 0 && isset($children[0]->id)){
                return UtilService::format_data(self::AJAX_FAIL, '请先删除子节点', '');
            }

            $row->delete();
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }
}

/**
 * @OA\Definition(
 *     definition="Orgnizations",
 *     type="array",
 *     @OA\Items(ref="#/definitions/Orgnization")
 * )
 */
