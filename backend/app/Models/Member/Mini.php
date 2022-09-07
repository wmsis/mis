<?php

namespace App\Models\Member;

use Illuminate\Database\Eloquent\Model;

class Mini extends Model
{
    protected  $table = 'mini';
    protected $fillable = ['openid', 'nickname', 'avatarurl', 'gender', 'city', 'province', 'country', 'platform', 'member_id']; //批量赋值

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function addresses(){
        return $this->hasMany('App\Models\Member\Address', 'mini_id');
    }

    public function member(){
        return $this->belongsTo('App\Models\Member\Member', 'member_id');
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
 * @OA\Definition(
 *     definition="Mini",
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
 *         property="avatarurl",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="gender",
 *         type="string"
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
 *         property="platform",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="member_id",
 *         type="integer"
 *     )
 * )
 */
