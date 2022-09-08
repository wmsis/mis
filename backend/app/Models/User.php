<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除
use App\Models\SIS\HistorianTag;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'desc', 'email', 'password', 'mobile', 'area', 'address', 'isopen', 'type', 'member_id', 'type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        //'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        //'email_verified_at' => 'datetime',
        'isopen' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    protected  $dates = ['deleted_at'];  //添加软删除

    public function roles(){
        return $this->belongsToMany('App\Models\Role', 'role_user', 'user_id', 'role_id')->withTimestamps();
    }

    public function orgnizations(){
        return $this->belongsToMany('App\Models\SIS\Orgnization', 'user_orgnization', 'user_id', 'orgnization_id')->withTimestamps();
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

    //判断用户是否有某个组织，某些组织
    public function isInOrgnizations($orgnizations){
        return !!$orgnizations->intersect($this->orgnizations)->count();   //两个感叹号返回布尔类型   intersect 方法返回两个集合的交集
    }

    //给用户分配组织
    public function assignOrgnization($orgnization){
        return $this->orgnizations()->save($orgnization);
    }

    //取消分配组织
    public function deleteOrgnization($orgnization){
        return $this->orgnizations()->detach($orgnization);
    }

    /*
     * 用户是否有某项菜单权限
     * 权限的角色和用户的角色是否有交集
     */
    public function hasPermission($permission){
        return $this->isInRoles($permission->roles);
    }

    /*
     * 用户是否有某项接口权限
     * 权限的角色和用户的角色是否有交集
     */
    public function hasApi($api){
        return $this->isInRoles($api->roles);
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
        return $this->belongsTo('App\Models\Member\Member');
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
        return ['role' => 'user'];
    }
}

/**
 * @OA\Definition(
 *     definition="User",
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
 *         property="mobile",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="desc",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="area",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="isopen",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string"
 *     )
 * )
 */
