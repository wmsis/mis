<?php

namespace App\Models\Mongo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


/**
 * @OA\Schema(
 *     title="HistorianFormatData Model",
 *     description="HistorianFormatData Model",
 *     @OA\Property(
 *         property="_id",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="dcs_standard_id",
 *         type="integer"
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
class HistorianFormatData extends Model
{
    protected $collection = 'historian_format_data_yongqiang2';     //文档名
    protected $primaryKey = '_id';    //设置id
    protected $fillable = ['dcs_standard_id', 'value', 'datetime'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function findRowByIdAndTime($dcs_standard_id, $datetime){
        return self::where('dcs_standard_id', $dcs_standard_id)->where('datetime', $datetime)->first();
    }
}
