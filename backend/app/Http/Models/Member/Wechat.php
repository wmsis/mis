<?php

namespace App\Http\Models\Member;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Http\Models\Member\Wechat
 *
 * @property int $id
 * @property string|null $headimgurl
 * @property string|null $nickname
 * @property string|null $openid
 * @property int|null $subscribe
 * @property string|null $city
 * @property string|null $province
 * @property string|null $country
 * @property string|null $unionid
 * @property string|null $gender
 * @property string|null $platform
 * @property int|null $member_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Member\Address[] $addresses
 * @property-read int|null $addresses_count
 * @property-read \App\Http\Models\Member\Member|null $member
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereHeadimgurl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereSubscribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Wechat extends Model
{
    protected  $table = 'wechat';
    protected $fillable = ['openid', 'nickname', 'gender', 'subscribe', 'city', 'province', 'country', 'headimgurl', 'unionid', 'platform', 'member_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function addresses(){
        return $this->hasMany('App\Http\Models\Member\Address', 'wechat_id');
    }

    public function member(){
        return $this->belongsTo('App\Http\Models\Member\Member', 'member_id');
    }

    public function findByOpenid($openid)
    {
        return $this->where('openid', $openid)->first();
    }
}

/**
 * @SWG\Definition(
 *     definition="Wechat",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="openid",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="nickname",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="gender",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="subscribe",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="city",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="province",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="country",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="headimgurl",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="unionid",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="platform",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="member_id",
 *         type="integer"
 *     )
 * )
 */
