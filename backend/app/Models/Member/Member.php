<?php

namespace App\Models\Member;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除
use UtilService;
use Illuminate\Support\Facades\DB;
use App\Models\Member\Wechat;

class Member extends Model
{
    use SoftDeletes;
    protected  $table = 'member';
    protected $fillable = [
        'username', 'password', 'mobile', 'consume_num', 'consume_money', 'last_consume_time', 'is_vip', 'card_no', 'channel',
        'is_distribute', 'money', 'total_income', 'today_income', 'point', 'code', 'parent_id', 'router', 'level', 'vip_save_money', 'freezen_money'
    ];

    protected  $dates = ['deleted_at'];  //添加软删除

    //属性转换
    protected $casts = [
        'is_vip' => 'boolean',  //将其由 integer 类型（0或1）转换为boolean 类型
        'is_distribute' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'last_interaction_time' => 'datetime:Y-m-d H:i:s'
    ];

    public function wechats(){
        return $this->hasMany('App\Models\Member\Wechat', 'member_id');
    }

    public function minis(){
        return $this->hasMany('App\Models\Member\Mini', 'member_id');
    }

    public function materials(){
        return $this->belongsToMany('App\Models\Wechat\Material', 'member_material', 'member_id', 'material_id')->withPivot('status')->withTimestamps();
    }

    public function findParentByWechatOpenid($openid)
    {
        $res = null;
        $self = $this->wechats()->where('openid', $openid)->first();
        if($self){
            $parent = $this->where('code', $self['parent_id'])->first();
            if($parent){
                $res = $parent;
            }
        }

        return $res;
    }

    public function findParentByMiniOpenid($openid)
    {
        $res = null;
        $self = $this->minis()->where('openid', $openid)->first();
        if($self){
            $parent = $this->where('parent_id', $self['member_id'])->first();
            if($parent){
                $res = $parent;
            }
        }

        return $res;
    }

    public function findChildrenByParent($parent_id)
    {
        return $this->where('parent_id', $parent_id)->get();
    }

    public function findByUnionId($unionid)
    {
        return $this->where('unionid', $unionid)->first();
    }

    public function findByCode($code)
    {
        return $this->where('code', $code)->first();
    }

    public function generateMember($params){
        $code = UtilService::random_str(6);
        DB::beginTransaction();
        try {
            $m_p = array(
                "unionid"=> $params['unionid'],
                "code"=> $code,
                "created_at"=>date('Y-m-d H:i:s'),
                "updated_at"=>date('Y-m-d H:i:s'),
            );
            $member_id = $this->insertGetId($m_p);

            $wx_p = array(
                "unionid"=> $params['unionid'],
                "openid"=> $params['openid'],
                "member_id"=> $member_id,
                "platform"=> $params['platform'],
                "code"=> $code,
                "nickname"=> $params['nickname'],
                "subscribe"=> $params['subscribe'],
                "gender"=> $params['sex'] ? 'man' : 'woman',
                "city"=> $params['city'],
                "province"=> $params['province'],
                "country"=> $params['country'],
                "headimgurl"=> $params['headimgurl']
            );
            Wechat::create($wx_p);

            if(isset($params['scode']) && $params['scode']){
                $parent = $this->findByCode($params['scode']);
                if($parent){
                    $parent_id = $parent->id;
                    $router = $parent->router + ',' . $member_id;

                    $this->where('id', $member_id)
                        ->update([
                            "router"=>$router,
                            "parent_id"=>$parent_id
                        ]);
                }
            }

            DB::commit();
            return $member_id;
        }
        catch (QueryException $ex) {
            DB::rollback();
            return false;
        }
    }
}

/**
 * @OA\Definition(
 *     definition="Member",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="username",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="mobile",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="consume_num",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="consume_money",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="last_consume_time",
 *         type="string",
 *         format="dateTime"
 *     ),
 *     @OA\Property(
 *         property="is_vip",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="card_no",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="is_distribute",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="money",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="freezen_money",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="total_income",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="today_income",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="point",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="code",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="router",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="level",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="vip_save_money",
 *         type="string"
 *     )
 * )
 */
