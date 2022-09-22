<?php

namespace App\Models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="HistorianData Model",
 *     description="HistorianData Model",
 *     @OA\Property(
 *         property="_id",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="cn_name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="en_name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="tag_name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="value",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="time",
 *         type="string"
 *     ),
 * )
 */

class HistorianData extends Model
{
    protected $connection = 'mongodb';  //库名
    protected $collection = 'historian_data';     //文档名
    protected $primaryKey = '_id';    //设置id
    protected $fillable = ['cn_name', 'en_name', 'tag_name', 'value', 'time'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
