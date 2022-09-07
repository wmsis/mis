<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


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
