<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除
use App\Http\Models\SIS\HistorianTag;

/**
 * App\User
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $desc
 * @property string|null $mobile
 * @property string|null $area
 * @property string|null $address
 * @property bool $isopen
 * @property string|null $password
 * @property string|null $type
 * @property string|null $preferences
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereIsopen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\User withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\SIS\HistorianTag[] $tags
 * @property-read int|null $tags_count
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $fillable = [
        'name', 'desc', 'email', 'password', 'mobile', 'area', 'address', 'isopen', 'type', 'period', 'member_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected  $hidden = [
        'password'
    ];

    protected  $dates = ['deleted_at'];  //添加软删除

    //属性转换
    protected $casts = [
        'isopen' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function roles(){
        return $this->belongsToMany('App\Role', 'role_user', 'user_id', 'role_id')->withTimestamps();
    }

    //判断用户是否有某个角色，某些角色
    public function isInRoles($roles){
        return !!$roles->intersect($this->roles)->count();   //两个感叹号返回布尔类型   intersect 方法返回两个集合的交集
    }

    //给用户分配角色
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

    public function findByEmail($email)
    {
        return $this->whereNull('deleted_at')->where('email', $email)->first();
    }

    public function findByMobile($mobile)
    {
        return $this->whereNull('deleted_at')->where('mobile', $mobile)->first();
    }

    public function isMobileExist($mobile)
    {
        return $this->where('mobile', $mobile)->withTrashed()->first();
    }

    public function member(){
        return $this->belongsTo('App\Http\Models\Member\Member');
    }

    public function tags(){
        return $this->belongsToMany('App\Http\Models\SIS\HistorianTag', 'tag_user', 'user_id', 'tag_id')->withTimestamps();
    }

    //判断用户是否有某个角色，某些角色
    public function isInTags($tags){
        return !!$tags->intersect($this->tags)->count();   //两个感叹号返回布尔类型   intersect 方法返回两个集合的交集
    }

    //给用户分配角色
    public function assignTag($tag){
        return $this->tags()->save($tag);
    }

    //取消分配角色
    public function deleteTag($tag){
        return $this->tags()->detach($tag);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}

/**
 * @SWG\Definition(
 *     definition="User",
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
 *         property="mobile",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="email",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="desc",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="password",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="area",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="address",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="isopen",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="type",
 *         type="string"
 *     )
 * )
 */
