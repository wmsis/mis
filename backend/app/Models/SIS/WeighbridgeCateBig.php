<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;

class WeighbridgeCateBig extends Model
{
    protected $table = 'weighbridge_cate_big';
    protected $fillable = ['name', 'description'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function small_names()
    {
        return $this->hasMany('App\Models\SIS\WeighbridgeCateSmall');
    }

    public function insertMany($params){
        return self::insert($params);
    }
}
