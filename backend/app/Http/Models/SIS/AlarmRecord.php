<?php

namespace App\Http\Models\SIS;

use Illuminate\Database\Eloquent\Model;

class AlarmRecord extends Model
{
    protected $table = 'alarm_record';
    protected $fillable = ['tag_id', 'start_time', 'end_time', 'upper_limit', 'lower_limit'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}

/**
 * @SWG\Definition(
 *     definition="AlarmRecord",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="tag_id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="start_time",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="end_time",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="upper_limit",
 *         type="float"
 *     ),
 *     @SWG\Property(
 *         property="lower_limit",
 *         type="float"
 *     ),
 * )
 */
