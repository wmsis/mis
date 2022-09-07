<?php

namespace App\Models\Wechat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除


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
        return $this->hasMany('App\Models\Wechat\Material', 'pic_txt_id');
    }
}


/**
 * @OA\Definition(
 *     definition="PicTxt",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string"
 *     )
 * )
 */
