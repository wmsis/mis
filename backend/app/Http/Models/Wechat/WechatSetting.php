<?php

namespace App\Http\Models\Wechat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Http\Models\Wechat\WechatSetting
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $appid
 * @property string|null $appsecret
 * @property string|null $token
 * @property string|null $platform 公众号名称
 * @property string|null $mchid
 * @property string|null $wx_pay_key 支付秘钥
 * @property string $apiclient_key
 * @property string $apiclient_cert
 * @property string|null $verify_file_txt 验证文件内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\WechatSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereApiclientCert($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereApiclientKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereAppid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereAppsecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereMchid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereVerifyFileTxt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereWxPayKey($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\WechatSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\WechatSetting withoutTrashed()
 * @mixin \Eloquent
 */
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
 * @SWG\Definition(
 *     definition="WechatSetting",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="user_id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="appid",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="appsecret",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="token",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="platform",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="mchid",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="wx_pay_key",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="apiclient_key",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="apiclient_cert",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="verify_file_txt",
 *         type="string"
 *     )
 * )
 */
