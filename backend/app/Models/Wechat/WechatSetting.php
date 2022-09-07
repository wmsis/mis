<?php

namespace App\Models\Wechat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WechatSetting extends Model
{
    use SoftDeletes;

    protected  $table = 'wechat_setting';
    protected $fillable = ['user_id', 'appid', 'appsecret', 'token', 'platform', 'mchid', 'wx_pay_key', 'apiclient_key', 'apiclient_cert', 'verify_file_txt']; //批量赋值
    protected  $dates = ['deleted_at'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}

/**
 * @OA\Definition(
 *     definition="WechatSetting",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="appid",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="appsecret",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="token",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="platform",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="mchid",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="wx_pay_key",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="apiclient_key",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="apiclient_cert",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="verify_file_txt",
 *         type="string"
 *     )
 * )
 */
