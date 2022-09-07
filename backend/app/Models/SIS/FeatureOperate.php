<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeatureOperate extends Model
{
    use softDeletes;
    protected $table = 'route_menu';
    protected $fillable = ['name', 'description', 'parent_id', 'level', 'url'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
