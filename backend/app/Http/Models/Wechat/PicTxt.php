<?php

namespace App\Http\Models\Wechat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除

/**
 * App\Http\Models\Wechat\PicTxt
 *
 * @property int $id
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Wechat\Material[] $materials
 * @property-read int|null $materials_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\PicTxt onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\PicTxt withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\PicTxt withoutTrashed()
 * @mixin \Eloquent
 */
class PicTxt extends Model
{
    use SoftDeletes;
    protected  $table = 'wx_pic_txt';

    //批量赋值
    protected $fillable = ['name'];

    //添加软删除, 日期修改器
    protected  $dates = ['deleted_at'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function materials(){
        return $this->hasMany('App\Http\Models\Wechat\Material', 'pic_txt_id');
    }
}


/**
 * @SWG\Definition(
 *     definition="PicTxt",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="name",
 *         type="string"
 *     )
 * )
 */