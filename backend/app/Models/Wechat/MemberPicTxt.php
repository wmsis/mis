<?php

namespace App\Models\Wechat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除


class MemberPicTxt extends Model
{
    use SoftDeletes;
    protected  $table = 'member_pic_txt';

    //批量赋值
    protected $fillable = ['type', 'pic_txt_id', 'openid', 'member_id', 'text', 'media_path', 'status', 'result'];

    //添加软删除, 日期修改器
    protected  $dates = ['deleted_at'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}

/**
 * @OA\Definition(
 *     definition="MemberPicTxt",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="pic_txt_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="openid",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="member_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="text",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         enum={"pictxt", "text", "image", "voice", "video"}
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"success", "fail"}
 *     ),
 *     @OA\Property(
 *         property="result",
 *         type="string"
 *     )
 * )
 */
