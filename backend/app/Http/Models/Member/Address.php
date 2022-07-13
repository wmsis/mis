<?php

namespace App\Http\Models\Member;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Http\Models\Member\Address
 *
 * @property int $id
 * @property int|null $mini_id
 * @property int|null $wechat_id
 * @property string|null $userName
 * @property string|null $postalCode
 * @property string|null $provinceName
 * @property string|null $cityName
 * @property string|null $countyName
 * @property string|null $detailInfo
 * @property string|null $nationalCode
 * @property string|null $telNumber
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Http\Models\Member\Mini|null $mini
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereCityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereCountyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereDetailInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereMiniId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereNationalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereProvinceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereTelNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereWechatId($value)
 * @mixin \Eloquent
 */
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
        return $this->belongsTo('App\Http\Models\Member\Mini', 'mini_id');
    }
}

/**
 * @SWG\Definition(
 *     definition="Address",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="mini_id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="wechat_id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="userName",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="postalCode",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="provinceName",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="cityName",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="countyName",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="detailInfo",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="nationalCode",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="telNumber",
 *         type="string"
 *     )
 * )
 */
