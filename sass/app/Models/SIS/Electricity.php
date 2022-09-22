<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\SIS\Electricity
 * author 叶文华
 * IEC104取得的电表数据
 */
class Electricity extends Model
{
    protected $table = 'electricity_yongqiang2';
    protected $fillable = ['address', 'value', 'actual_value', 'quality', 'factor', 'cn_name'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function findById($id){
        return self::find($id);
    }

    public function insertOne($params){
        return self::create($params);
    }

    public function insertMany($params){
        return self::insert($params);
    }

    public function findByDateAndHour($date, $hour){
        $begin = strtotime($date.' '.$hour.':00:00');
        $end = strtotime($date.' '.$hour.':59:59');
        $rows = self::where('created_at', '>=', $begin)
            ->where('created_at', '<=', $end)
            ->get();

        return $rows;
    }
}

/**
 * @OA\Definition(
 *     definition="Electricity",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="number"
 *     ),
 *     @OA\Property(
 *         property="value",
 *         type="number"
 *     ),
 *     @OA\Property(
 *         property="actual_value",
 *         type="float"
 *     ),
 *     @OA\Property(
 *         property="quality",
 *         type="number"
 *     ),
 *     @OA\Property(
 *         property="factor",
 *         type="float"
 *     ),
 *     @OA\Property(
 *         property="cn_name",
 *         type="string"
 *     ),
 * )
 */
