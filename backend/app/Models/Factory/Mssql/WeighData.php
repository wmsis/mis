<?php

namespace App\Models\Factory\Mssql;

use Illuminate\Database\Eloquent\Model;
use Log;
/**
 * App\Models\SIS\WeighBridge
 * author 叶文华
 * 托利多地磅
 */
class WeighData extends Model
{
    protected $table = 'toledo';
    protected $fillable = ['truckno', 'productcode', 'product', 'firstweight', 'secondweight', 'firstdatetime', 'seconddatetime', 'grossdatetime', 'taredatetime',
    'sender', 'transporter', 'receiver', 'gross', 'tare', 'net', 'datastatus', 'id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
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

    public function findByWeighId($id){
        return self::where('weighid', $id)->first();
    }

    public function destroyByWeighId($id){
        return self::where('weighid', $id)->delete();
    }

    public function findLatestReport(){
        return self::orderBy("taredatetime", "DESC")->first();
    }

}
