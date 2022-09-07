<?php

namespace App\Models\Member;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected  $table = 'address';
    protected $fillable = ['mini_id', 'wechat_id', 'userName', 'postalCode', 'provinceName', 'cityName', 'countyName', 'detailInfo', 'nationalCode', 'telNumber']; //批量赋值

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    /**
     * 属于关系
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mini(){
        return $this->belongsTo('App\Models\Member\Mini', 'mini_id');
    }
}

/**
 * @OA\Definition(
 *     definition="Address",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="mini_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="wechat_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="userName",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="postalCode",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="provinceName",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="cityName",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="countyName",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="detailInfo",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="nationalCode",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="telNumber",
 *         type="string"
 *     )
 * )
 */
