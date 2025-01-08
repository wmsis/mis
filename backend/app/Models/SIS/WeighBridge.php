<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Log;
/**
 * App\Models\SIS\WeighBridge
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

    public function destroyByWeighId($id){
        return self::where('weighid', $id)->delete();
    }

    public function findLatestReport(){
        return self::orderBy("taredatetime", "DESC")->first();
    }

}

/**
 * @OA\Definition(
 *     definition="WeighBridge",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="truckno",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="productcode",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="product",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="firstweight",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="secondweight",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="firstdatetime",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="seconddatetime",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="grossdatetime",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="taredatetime",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="sender",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="transporter",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="receiver",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="gross",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="tare",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="net",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="datastatus",
 *         type="integer"
 *     ),
 * )
 */
