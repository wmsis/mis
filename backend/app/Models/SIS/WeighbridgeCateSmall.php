<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;

class WeighbridgeCateSmall extends Model
{
    protected $table = 'weighbridge_cate_small';
    protected $fillable = ['name'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function big_name()
    {
        return $this->belongsTo('App\Models\SIS\WeighbridgeCateBig');
    }
}
