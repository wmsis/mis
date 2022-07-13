<?php

namespace App\Http\Models\Member;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Http\Models\Member\Mini
 *
 * @property int $id
 * @property string|null $unionid
 * @property string|null $openid
 * @property string|null $nickname
 * @property string|null $avatarurl
 * @property string $gender
 * @property string|null $city
 * @property string|null $province
 * @property string|null $country
 * @property string|null $platform
 * @property int|null $member_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Member\Address[] $addresses
 * @property-read int|null $addresses_count
 * @property-read \App\Http\Models\Member\Member|null $member
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereAvatarurl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Mini extends Model
{
    protected  $table = 'mini';
    protected $fillable = ['openid', 'nickname', 'avatarurl', 'gender', 'city', 'province', 'country', 'platform', 'member_id']; //批量赋值

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function addresses(){
        return $this->hasMany('App\Http\Models\Member\Address', 'mini_id');
    }

    public function member(){
        return $this->belongsTo('App\Http\Models\Member\Member', 'member_id');
    }

    public function lists()
    {
        return $this->get();
    }

    public function findByOpenid($openid)
    {
        return $this->where('openid', $openid)->first();
    }
}

/**
 * @SWG\Definition(
 *     definition="Mini",
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
 *         property="avatarurl",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="gender",
 *         type="string"
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
 *         property="platform",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="member_id",
 *         type="integer"
 *     )
 * )
 */
