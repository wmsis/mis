<?php

namespace App\Models\Member;

use Illuminate\Database\Eloquent\Model;

class Wechat extends Model
{
    protected  $table = 'wechat';
    protected $fillable = ['openid', 'nickname', 'gender', 'subscribe', 'city', 'province', 'country', 'headimgurl', 'unionid', 'platform', 'member_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function addresses(){
        return $this->hasMany('App\Models\Member\Address', 'wechat_id');
    }

    public function member(){
        return $this->belongsTo('App\Models\Member\Member', 'member_id');
    }

    public function findByOpenid($openid)
    {
        return $this->where('openid', $openid)->first();
    }
}

/**
 * @OA\Definition(
 *     definition="Wechat",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="openid",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="nickname",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="gender",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="subscribe",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="city",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="province",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="country",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="headimgurl",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="unionid",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="platform",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="member_id",
 *         type="integer"
 *     )
 * )
 */
