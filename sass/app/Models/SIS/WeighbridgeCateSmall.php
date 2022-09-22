<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;

class WeighbridgeCateSmall extends Model
{
    protected $table = 'weighbridge_cate_small';
    protected $fillable = ['name', 'description', 'weighbridge_cate_big_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function weighbridge_cate_big()
    {
        return $this->belongsTo('App\Models\SIS\WeighbridgeCateBig');
    }

    public function insertOne($params){
        return self::create($params);
    }

    public function updateOne($params, $where){
        $i = 0;
        $obj = null;
        foreach ($where as $key => $value) {
            if($i == 0){
                $obj = self::where($key, $value);
            }
            else{
                $obj = $obj->where($key, $value);
            }
            $i++;
        }
        return $obj->update($params);
    }

    public function insertMany($params){
        return self::insert($params);
    }
}
