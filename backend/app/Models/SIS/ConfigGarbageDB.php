<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigGarbageDB extends Model
{
    use softDeletes;
    protected $table = 'config_garbage_db';
    protected $fillable = ['type', 'user', 'password', 'ip', 'port', 'db_name', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
