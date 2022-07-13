<?php

namespace App\Http\Models\Member;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Http\Models\Member\TakeMoney
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\TakeMoney newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\TakeMoney newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\TakeMoney query()
 * @mixin \Eloquent
 */
class TakeMoney extends Model
{
    protected  $table = 'takemoney';
    protected $fillable = ['admin_id', 'member_id', 'money', 'pay_no', 'status', 'formid', 'is_notify', 'openid']; //批量赋值

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
