<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Http\Models\OperateLog
 *
 * @property int $id
 * @property int $user_id
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\OperateLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\OperateLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\OperateLog withoutTrashed()
 * @mixin \Eloquent
 */
class OperateLog extends Model
{
    use SoftDeletes;
    protected  $table = 'operate_log';
    protected  $fillable = ['user_id', 'description']; //批量赋值
    protected  $dates = ['deleted_at'];  //添加软删除

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

}
