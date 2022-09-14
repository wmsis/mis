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
use App\Models\SIS\Orgnization;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Http\Requests\User\StoreRoleRequest;
use App\Models\Role;
use Log;

class OrgnizationController extends Controller
{
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
        $data = Orgnization::where('level', 3)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $data);
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
                    'sort' => $item->sort,
                    'parent_id' => $item->parent_id,
                    'level' => $item->level,
                    'description' => $item->description,
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
        $description = $request->input('description');
        $level = 1;
        $parent_id = $request->input('parent_id');
        $sort = $request->input('sort');

        DB::beginTransaction();
        try {
            if ($id) {
                $row = Orgnization::find($id);
                $row->name = $name;
                $row->code = $code;
                $row->description = $description;
                $row->parent_id = $parent_id;
                $row->sort = $sort;
                $row->save();
            }
            else {
                $params = request(['name', 'code', 'description', 'parent_id', 'sort']);
                if($parent_id){
                    $parent = Orgnization::find($parent_id);
                    $level = $parent && $parent->level ? $parent->level + 1 : 1;
                }
                $params['level'] = $level;
                Orgnization::create($params); //save 和 create 的不同之处在于 save 接收整个 Eloquent 模型实例而 create 接收原生 PHP 数组
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
     *     path="/api/orgnizations/{id}/role",
     *     tags={"公司组织架构orgnizations"},
     *     operationId="getOrgRole",
     *     summary="用户组织角色",
     *     description="使用说明：获取用户组织角色",
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
        $roles = Role::all(); // all roles
        $myRoles = $orgnization->roles; //带括号的是返回关联对象实例，不带括号是返回动态属性

        //compact 创建一个包含变量名和它们的值的数组
        $data = compact('roles', 'myRoles');
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $data);
    }

    /**
     * @OA\Post(
     *     path="/api/orgnizations/{id}/role",
     *     tags={"公司组织架构orgnizations"},
     *     operationId="storeOrgRole",
     *     summary="保存用户组织角色",
     *     description="使用说明：保存用户组织角色",
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

        return UtilService::format_data(self::AJAX_SUCCESS, '保存成功', []);
    }

    /**
     * @OA\Post(
     *     path="/api/orgnizations/delete",
     *     tags={"公司组织架构orgnizations"},
     *     operationId="deleteOrgnization",
     *     summary="删除用户组织",
     *     description="使用说明：删除用户组织",
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
        $res = $row->delete();
        if($row && $res){
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
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
