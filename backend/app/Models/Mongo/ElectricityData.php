<?php

namespace App\Models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="ElectrityData Model",
 *     description="ElectrityData Model",
 *     @OA\Property(
 *         property="_id",
 *         type="string"
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
 *         type="number"
 *     ),
 *     @OA\Property(
 *         property="quality",
 *         type="number"
 *     ),
 *     @OA\Property(
 *         property="factor",
 *         type="number"
 *     ),
 *     @OA\Property(
 *         property="cn_name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="datetime",
 *         type="string"
 *     ),
 * )
 */

class ElectricityData extends Model
{
    protected $connection = 'mongodb';  //库名
    protected $collection = 'historian_data';     //文档名
    protected $primaryKey = '_id';    //设置id
    protected $fillable = ['cn_name', 'address', 'actual_value', 'value', 'factor', 'quality', 'datetime'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
