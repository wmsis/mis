<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SysUserMap extends Model
{
    use softDeletes;
    protected $table = 'sys_user_map';
    protected $fillable = ['basic_sys_name', 'basic_conn_name', 'basic_domian', 'basic_user_id', 'basic_login_path', 'target_sys_name', 'target_conn_name', 'target_domian', 'target_user_id', 'target_login_path'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
