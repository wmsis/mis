<?php

/**
 * App\Models\Factory\DcsData
 * author 叶文华
 * IEC104 电厂本地DCS数据，存于MongoDB
 */
namespace App\Models\Factory;

use Jenssegers\Mongodb\Eloquent\Model;
use Log;

/**
 * @OA\Schema(
 *     title="DcsData Model",
 *     description="DcsData Model",
 *     @OA\Property(
 *         property="_id",
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
 *         property="datetime",
 *         type="string"
 *     ),
 * )
 */
class DcsData extends Model
{
    protected $collection = 'history';
    protected $primaryKey = '_id';    //设置id
    protected $fillable = ['tag_name', 'value', 'datetime'];

    protected $casts = [
        'createdAt' => 'datetime:Y-m-d H:i:s',
        'updatedAt' => 'datetime:Y-m-d H:i:s'
    ];
}
