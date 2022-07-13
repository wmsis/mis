<?php

namespace App\Http\Models\Wechat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除

/**
 * App\Http\Models\Wechat\MemberPicTxt
 *
 * @property int $id
 * @property string|null $openid
 * @property string|null $type
 * @property int|null $pic_txt_id
 * @property int|null $member_id
 * @property string|null $text
 * @property string|null $media_path
 * @property string|null $status
 * @property string|null $result
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\MemberPicTxt onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereMediaPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt wherePicTxtId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\MemberPicTxt withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\MemberPicTxt withoutTrashed()
 * @mixin \Eloquent
 */
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
 * @SWG\Definition(
 *     definition="MemberPicTxt",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="pic_txt_id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="openid",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="member_id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="text",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="type",
 *         type="string",
 *         enum={"pictxt", "text", "image", "voice", "video"}
 *     ),
 *     @SWG\Property(
 *         property="status",
 *         type="string",
 *         enum={"success", "fail"}
 *     ),
 *     @SWG\Property(
 *         property="result",
 *         type="string"
 *     )
 * )
 */
