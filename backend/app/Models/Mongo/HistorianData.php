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

class HistorianData extends Model
{
    protected $primaryKey = '_id';    //设置id
    protected $fillable = ['tag_name', 'value', 'datetime'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function insertMany($params){
        return self::insert($params);
    }

    public function findRowBySn($sn){
        return self::where('_id', $sn)->first();
    }

    public function findRowByTagAndTime($tag_name, $datetime){
        return self::where('tag_name', $tag_name)->where('datetime', $datetime)->first();
    }
}
