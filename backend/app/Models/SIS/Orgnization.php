<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orgnization extends Model
{
    use softDeletes;
    protected $table = 'orgnization';
    protected $fillable = ['name', 'description', 'parent_id', 'level', 'sort', 'code', 'sub_title', 'ancestor_id', 'longitude', 'latitude', 'address', 'status', 'electricity_ability'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_orgnization', 'orgnization_id', 'role_id')->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_orgnization', 'orgnization_id', 'user_id')->withTimestamps();
    }

    public function childs() {
        return $this->hasMany('App\Models\SIS\Orgnization', 'parent_id');
    }

    public function parent() {
        return $this->belongsTo('App\Models\SIS\Orgnization', 'parent_id');
    }

    //判断用户组是否有某个角色，某些角色
    public function isInRoles($roles){
        return !!$roles->intersect($this->roles)->count();   //两个感叹号返回布尔类型   intersect 方法返回两个集合的交集
    }

    //给用户组分配角色
    public function assignRole($role){
        return $this->roles()->save($role);
    }

    //取消分配角色
    public function deleteRole($role){
        return $this->roles()->detach($role);
    }

    /*
     * 用户是否有某项权限
     * 权限的角色和用户的角色是否有交集
     */
    public function hasPermission($permission){
        return $this->isInRoles($permission->roles);
    }

    public function roots()
    {
        return $this->whereNull('deleted_at')
            ->where('level', 1)
            ->orderBy('sort', 'asc')
            ->get();
    }

    public function children($parent_id)
    {
        return $this->whereNull('deleted_at')
            ->where('parent_id', $parent_id)
            ->orderBy('sort', 'asc')
            ->get();
    }
}

/**
 * @OA\Definition(
 *     definition="Orgnization",
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
 *         property="description",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="level",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="longitude",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="latitude",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="electricity_ability",
 *         type="string"
 *     )
 * )
 */
