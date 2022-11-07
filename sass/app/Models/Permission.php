<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除


class Permission extends Model
{
    use SoftDeletes;
    protected  $table = 'permissions';
    protected  $fillable = ['name', 'icon', 'color', 'page_url', 'level', 'sort', 'type', 'api_name', 'parent_id', 'is_show']; //批量赋值
    protected  $dates = ['deleted_at'];  //添加软删除
    protected  $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    //权限属于哪个角色
    public function roles(){
        return $this->belongsToMany('App\Models\Role', 'permission_role', 'permission_id', 'role_id')->withTimestamps();
    }

    public function lists()
    {
        return $this->whereNull('deleted_at')
            ->where('level', 1)
            ->where('is_show', 1)
            ->orderBy('sort', 'asc')
            ->get();
    }

    public function insert($params)
    {
        return $this->insertGetId($params);
    }

    public function rowById($id){
        return $this->select('*')->where('id', $id)->first();
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
 *     definition="Permission",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="api_name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="color",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="icon",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="level",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="page_url",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="sort",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string"
 *     )
 * )
 */
