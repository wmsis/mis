<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenement extends Model
{
    use softDeletes, HasFactory;

    protected $connection = 'mysql_mis';
    protected $table = 'tenement';
    protected $fillable = ['ip', 'username', 'code', 'db_name', 'db_user', 'db_pwd', 'memo'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
