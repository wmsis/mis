<?php

namespace App\Http\Models\Member;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除
use UtilService;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Member\Wechat;

/**
 * App\Http\Models\Member\Member
 *
 * @property int $id
 * @property string|null $unionid
 * @property string|null $password
 * @property string|null $username
 * @property string|null $mobile
 * @property int|null $consume_num 总消费次数
 * @property float|null $consume_money 总消费金额
 * @property string|null $last_consume_time 上次消费时间
 * @property bool|null $is_vip 是否会员
 * @property string|null $card_no
 * @property bool|null $is_distribute 是否分销
 * @property float|null $money 余额
 * @property float $freezen_money 冻结余额
 * @property float|null $total_income
 * @property float|null $today_income
 * @property int|null $point 积分
 * @property string|null $code
 * @property int|null $parent_id
 * @property string|null $router
 * @property string|null $level
 * @property float|null $vip_save_money
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Wechat\Material[] $materials
 * @property-read int|null $materials_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Member\Mini[] $minis
 * @property-read int|null $minis_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Member\Wechat[] $wechats
 * @property-read int|null $wechats_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Member\Member onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereCardNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereConsumeMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereConsumeNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereFreezenMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereIsDistribute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereIsVip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereLastConsumeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member wherePoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereRouter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereTodayIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereTotalIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereVipSaveMoney($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Member\Member withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Member\Member withoutTrashed()
 * @mixin \Eloquent
 */
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
        return $this->hasMany('App\Http\Models\Member\Wechat', 'member_id');
    }

    public function minis(){
        return $this->hasMany('App\Http\Models\Member\Mini', 'member_id');
    }

    public function materials(){
        return $this->belongsToMany('App\Http\Models\Wechat\Material', 'member_material', 'member_id', 'material_id')->withPivot('status')->withTimestamps();
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
 * @SWG\Definition(
 *     definition="Member",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="username",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="password",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="mobile",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="consume_num",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="consume_money",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="last_consume_time",
 *         type="string",
 *         format="dateTime"
 *     ),
 *     @SWG\Property(
 *         property="is_vip",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="card_no",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="is_distribute",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="money",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="freezen_money",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="total_income",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="today_income",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="point",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="code",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="parent_id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="router",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="level",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="vip_save_money",
 *         type="string"
 *     )
 * )
 */
