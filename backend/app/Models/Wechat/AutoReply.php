<?php

namespace App\Models\Wechat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除


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
        return $this->belongsTo('App\Models\Wechat\PicTxt', 'pic_txt_id');
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
 * @OA\Definition(
 *     definition="AutoReply",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="category",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         enum={"text", "pic_txt", "img"}
 *     ),
 *     @OA\Property(
 *         property="text",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="pic_txt_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="img",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="keyword",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="interval_time",
 *         type="integer"
 *     )
 * )
 */
