<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除

/**
 * App\Permission
 *
 * @property int $id
 * @property string|null $path
 * @property string|null $name
 * @property string|null $icon
 * @property string|null $color
 * @property string|null $page_url
 * @property int|null $is_root
 * @property int|null $level
 * @property int|null $sort
 * @property string|null $api_name
 * @property string|null $api_url 接口列表
 * @property string|null $type
 * @property string|null $relations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Permission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereApiName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereApiUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereIsRoot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission wherePageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereRelations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Permission withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Permission withoutTrashed()
 * @mixin \Eloquent
 */
class Permission extends Model
{
    use SoftDeletes;
    protected  $table = 'permissions';
    protected  $fillable = ['path', 'name', 'icon', 'color', 'page_url', 'is_root', 'level', 'sort', 'api_name', 'api_url', 'type']; //批量赋值
    protected  $dates = ['deleted_at'];  //添加软删除
    protected  $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    //权限属于哪个角色
    public function roles(){
        return $this->belongsToMany('App\Role', 'permission_role', 'permission_id', 'role_id')->withTimestamps();
    }

    public function lists()
    {
        return $this->whereNull('deleted_at')
            ->where('level', 1)
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

    public function rowByPath($path){
        return $this->select('*')->where('path', $path)->first();
    }

    public function children($path)
    {
        $key = $path.'/%';
        $parent = $this->rowByPath($path);
        if($parent) {
            return $this->whereNull('deleted_at')
                ->where('level', $parent->level + 1)
                ->where('path', 'like', $key)
                ->orderBy('sort', 'asc')
                ->get();
        }
        else{
            return [];
        }
    }

    //所有后代
    public function descendant($path)
    {
        $key = $path.'/%';
        return $this->whereNull('deleted_at')
            ->where('path','like', $key)
            ->orderBy('sort', 'asc')
            ->get();
    }
}

/**
 * @SWG\Definition(
 *     definition="Permission",
 *     type="object",
 *     required={"name"},
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="api_name",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="api_url",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="color",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="icon",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="is_root",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="level",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="page_url",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="path",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="relations",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="sort",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="type",
 *         type="string"
 *     )
 * )
 */
