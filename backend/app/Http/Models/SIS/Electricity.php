<?php

namespace App\Http\Models\SIS;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Http\Models\SIS\Electricity
 * author 叶文华
 * IEC104取得的电表数据
 */
class Electricity extends Model
{
    protected $table = 'electricity';
    protected $fillable = ['address', 'value', 'actual_value', 'quality', 'factor', 'cn_name'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}

/**
 * @SWG\Definition(
 *     definition="Electricity",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="address",
 *         type="number"
 *     ),
 *     @SWG\Property(
 *         property="value",
 *         type="number"
 *     ),
 *     @SWG\Property(
 *         property="actual_value",
 *         type="float"
 *     ),
 *     @SWG\Property(
 *         property="quality",
 *         type="number"
 *     ),
 *     @SWG\Property(
 *         property="factor",
 *         type="float"
 *     ),
 *     @SWG\Property(
 *         property="cn_name",
 *         type="string"
 *     ),
 * )
 */
