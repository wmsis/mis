<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Log;

class WeighBridgeFormat extends Model
{
    protected $table = 'weighbridge_format_yongqiang2';
    protected $fillable = ['grossdatetime', 'taredatetime', 'net', 'weighid', 'weighbridge_cate_small_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function destroyByWeighId($id){
        return self::where('weighid', $id)->delete();
    }

    public function insertMany($params){
        return self::insert($params);
    }
}
