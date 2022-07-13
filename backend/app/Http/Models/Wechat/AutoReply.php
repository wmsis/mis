<?php

namespace App\Http\Models\Wechat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除

/**
 * App\Http\Models\Wechat\AutoReply
 *
 * @property int $id
 * @property string|null $category
 * @property string|null $type
 * @property string|null $text
 * @property int|null $pic_txt_id
 * @property string|null $img
 * @property string|null $keyword 关键词回复时需要
 * @property int|null $interval_time 间隔时间（分钟），打开回复时需要
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Models\Wechat\PicTxt|null $picTxt
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\AutoReply onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereIntervalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply wherePicTxtId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\AutoReply withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\AutoReply withoutTrashed()
 * @mixin \Eloquent
 */
class AutoReply extends Model
{
    use SoftDeletes;
    protected  $table = 'auto_reply';

    //批量赋值
    protected $fillable = ['category', 'type', 'pic_txt_id', 'img', 'text', 'keyword', 'interval_time'];

    //添加软删除, 日期修改器
    protected  $dates = ['deleted_at'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function picTxt(){
        return $this->belongsTo('App\Http\Models\Wechat\PicTxt', 'pic_txt_id');
    }

    public function findByCategory($category)
    {
        return $this->where('category', $category)->first();
    }

    public function findByKeyword($keyword)
    {
        return $this->where('keyword', $keyword)->first();
    }

    public function keywords()
    {
        return $this->whereNull('deleted_at')
            ->where('category', 'keyword')
            ->get();
    }
}


/**
 * @SWG\Definition(
 *     definition="AutoReply",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="category",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="type",
 *         type="string",
 *         enum={"text", "pic_txt", "img"}
 *     ),
 *     @SWG\Property(
 *         property="text",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="pic_txt_id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="img",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="keyword",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="interval_time",
 *         type="integer"
 *     )
 * )
 */