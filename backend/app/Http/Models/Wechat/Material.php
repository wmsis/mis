<?php

namespace App\Http\Models\Wechat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除

/**
 * App\Http\Models\Wechat\Material
 *
 * @property int $id
 * @property string|null $img
 * @property string|null $title
 * @property string $description
 * @property string|null $url
 * @property int|null $sort
 * @property int|null $send_num
 * @property int|null $read_num
 * @property int|null $buy_num
 * @property string|null $type
 * @property int|null $pic_txt_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Member\Member[] $members
 * @property-read int|null $members_count
 * @property-read \App\Http\Models\Wechat\PicTxt $pictxt
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\Material onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereBuyNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material wherePicTxtId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereReadNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereSendNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\Material withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\Material withoutTrashed()
 * @mixin \Eloquent
 */
class Material extends Model
{
    use SoftDeletes;
    protected  $table = 'wx_material';

    //批量赋值
    protected $fillable = ['img', 'title', 'description', 'url', 'sort', 'send_num', 'read_num', 'buy_num', 'type', 'pic_txt_id'];

    //添加软删除, 日期修改器
    protected  $dates = ['deleted_at'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function pictxt(){
        return $this->belongsTo('App\Http\Models\Wechat\PicTxt');
    }

    public function members(){
        return $this->belongsToMany('App\Http\Models\Member\Member', 'member_material', 'material_id', 'member_id')->withPivot('status')->withTimestamps();
    }
}

/**
 * @SWG\Definition(
 *     definition="Material",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="img",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="title",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="description",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="url",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="sort",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="send_num",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="read_num",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="buy_num",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="type",
 *         type="string",
 *         enum={"pictxt"}
 *     ),
 *     @SWG\Property(
 *         property="pic_txt_id",
 *         type="integer"
 *     )
 * )
 */
