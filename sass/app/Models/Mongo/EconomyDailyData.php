<?php

namespace App\Models\Mongo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="EconomyDailyData Model",
 *     description="EconomyDailyData Model",
 *     @OA\Property(
 *         property="_id",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="dcs_standard_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="avg_value",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="min_value",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="max_value",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string"
 *     ),
 * )
 */
class EconomyDailyData extends Model
{
    protected $primaryKey = '_id';    //设置id
    protected $fillable = ['dcs_standard_id', 'avg_value','min_value', 'max_value', 'date'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function findByIdAndDate($dcs_standard_id, $date){
        return self::where('dcs_standard_id', $dcs_standard_id)->where('date', $date)->first();
    }
}
