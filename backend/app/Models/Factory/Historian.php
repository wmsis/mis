<?php

/**
 * App\Models\Factory\Historian
 * author 叶文华
 * IEC104 电厂本地DCS数据，存于MongoDB
 */
namespace App\Models\Factory;

use Jenssegers\Mongodb\Eloquent\Model;
use Log;

/**
 * @OA\Schema(
 *     title="Historian Model",
 *     description="Historian Model",
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
class Historian extends Model
{
    protected $collection = 'hitorian';
    protected $primaryKey = '_id';    //设置id
    protected $fillable = ['tag_name', 'value', 'datetime'];

    protected $casts = [
        'createdAt' => 'datetime:Y-m-d H:i:s',
        'updatedAt' => 'datetime:Y-m-d H:i:s'
    ];

    public function findByDate($date){
        $begin = strtotime($date.' 00:00:00');
        $end = strtotime($date.' 23:59:59');
        $rows = self::where('datetime', '>=', $begin)
            ->where('datetime', '<=', $end)
            ->get();

        return $rows;
    }
}
