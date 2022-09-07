<?php

namespace App\Models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model;

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

/**
 * @SWG\Definition(
 *     definition="HistorianData",
 *     type="object",
 *     @SWG\Property(
 *         property="_id",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="cn_name",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="en_name",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="tag_name",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="value",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="time",
 *         type="string"
 *     ),
 * )
 */
