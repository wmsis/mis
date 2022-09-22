<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigHistorianDB extends Model
{
    use softDeletes;
    protected $table = 'config_historian_db';
    protected $fillable = ['user', 'password', 'ip', 'port', 'version', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
