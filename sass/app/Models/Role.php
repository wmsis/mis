<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除


class Role extends Model
{
    use SoftDeletes;
    protected  $table = 'roles';
    protected  $fillable = ['name', 'desc', 'type']; //批量赋值
    protected  $dates = ['deleted_at'];  //添加软删除
    protected  $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    //角色的所有菜单权限
    public function permissions(){
        return $this->belongsToMany('App\Models\Permission', 'permission_role', 'role_id', 'permission_id')->withTimestamps();
    }

    public function users(){
        return $this->belongsToMany('App\Models\User', 'role_user', 'role_id', 'user_id')->withTimestamps();
    }

    public function orgnizations(){
        return $this->belongsToMany('App\Models\SIS\Orgnization', 'role_orgnization', 'role_id', 'orgnization_id')->withTimestamps();
    }

    //给角色分配菜单权限
    public function grantPermission($permission){
        return $this->permissions()->save($permission);
    }

    public function grantPermissionDetail($value, $permission){
        return DB::table('permission_role')
            ->where('role_id', '=', $this->id)
            ->where('permission_id', '=', $permission->id)
            ->update($value);
    }

    //取消角色分配菜单权限
    public function deletePermission($permission){
        return $this->permissions()->detach($permission);
    }

    //是否有某项菜单权限
    public function hasPermission($permission){
        return $this->permissions->contains($permission);
    }

    //角色的所有接口权限
    public function apis(){
        return $this->belongsToMany('App\Models\SIS\API', 'role_api', 'role_id', 'api_id')->withTimestamps();
    }

    //给角色分配接口权限
    public function grantApi($api){
        return $this->apis()->save($api);
    }

    public function grantApiDetail($value, $api){
        return DB::table('role_api')
            ->where('role_id', '=', $this->id)
            ->where('api_id', '=', $api->id)
            ->update($value);
    }

    //取消角色分配接口权限
    public function deleteApi($api){
        return $this->apis()->detach($api);
    }

    //是否有某项接口权限
    public function hasApi($api){
        return $this->apis->contains($api);
    }
}

/**
 * @OA\Definition(
 *     definition="Role",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="desc",
 *         type="string"
 *     )
 * )
 */
