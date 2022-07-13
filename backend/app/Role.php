<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除

/**
 * App\Role
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $desc
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Role onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Role withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Role withoutTrashed()
 * @mixin \Eloquent
 */
class Role extends Model
{
    use SoftDeletes;
    protected  $table = 'roles';
    protected  $fillable = ['name', 'desc']; //批量赋值
    protected  $dates = ['deleted_at'];  //添加软删除
    protected  $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    //角色的所有权限
    public function permissions(){
        return $this->belongsToMany('App\Permission', 'permission_role', 'role_id', 'permission_id')->withTimestamps();
    }

    public function users(){
        return $this->belongsToMany('App\User', 'role_user', 'role_id', 'user_id')->withTimestamps();
    }

    //给角色分配权限
    public function grantPermission($permission){
        return $this->permissions()->save($permission);
    }

    public function grantPermissionDetail($value, $permission){
        return DB::table('permission_role')
            ->where('role_id', '=', $this->id)
            ->where('permission_id', '=', $permission->id)
            ->update($value);
    }

    //取消角色分配权限
    public function deletePermission($permission){
        return $this->permissions()->detach($permission);
    }

    //是否有某项权限
    public function hasPermission($permission){
        return $this->permissions->contains($permission);
    }
}

/**
 * @SWG\Definition(
 *     definition="Role",
 *     type="object",
 *     required={"name"},
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="desc",
 *         type="string"
 *     )
 * )
 */
