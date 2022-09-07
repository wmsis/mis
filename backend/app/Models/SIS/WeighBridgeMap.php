<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeighBridgeMap extends Model
{
    use softDeletes;
    protected $table = 'weighbridge_map';
    protected $fillable = ['cate_big_id', 'cate_small_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
