<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigElectricityDB extends Model
{
    use softDeletes;
    protected $table = 'config_electricity_db';
    protected $fillable = ['master_ip', 'slave_ip', 'common_addr', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
