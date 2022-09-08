<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $connection = 'mysql_mis';  //连接名
    protected $table = 'admin';

    //配置 保持一直
    protected $guard = 'admin';

    //隐藏不需要输出
    protected $hidden = [
        'password'
    ];

    //默认写法 env的key
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @description 自定义声明。给空即可
     * @Date 2021-12-24 0024 11:52
     */
    public function getJWTCustomClaims()
    {
        return ['role' => 'admin'];
    }
}
