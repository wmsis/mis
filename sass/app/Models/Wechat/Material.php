<?php

namespace App\Models\Wechat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除


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
        return $this->belongsTo('App\Models\Wechat\PicTxt');
    }

    public function members(){
        return $this->belongsToMany('App\Models\Member\Member', 'member_material', 'material_id', 'member_id')->withPivot('status')->withTimestamps();
    }
}

/**
 * @OA\Definition(
 *     definition="Material",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="img",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="url",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="sort",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="send_num",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="read_num",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="buy_num",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         enum={"pictxt"}
 *     ),
 *     @OA\Property(
 *         property="pic_txt_id",
 *         type="integer"
 *     )
 * )
 */
