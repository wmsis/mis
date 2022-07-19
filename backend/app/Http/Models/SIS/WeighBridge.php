<?php

namespace App\Http\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Log;
/**
 * App\Http\Models\SIS\WeighBridge
 * author 叶文华
 * 托利多地磅
 */
class WeighBridge extends Model
{
    protected $table = 'weighbridge_yongqiang2';
    protected $fillable = ['truckno', 'productcode', 'product', 'firstweight', 'secondweight', 'firstdatetime', 'seconddatetime', 'grossdatetime', 'taredatetime',
    'sender', 'transporter', 'receiver', 'gross', 'tare', 'net', 'datastatus', 'weighid'];

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

}

/**
 * @SWG\Definition(
 *     definition="WeighBridge",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="truckno",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="productcode",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="product",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="firstweight",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="secondweight",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="firstdatetime",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="seconddatetime",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="grossdatetime",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="taredatetime",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="sender",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="transporter",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="receiver",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="gross",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="tare",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="net",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="datastatus",
 *         type="integer"
 *     ),
 * )
 */
