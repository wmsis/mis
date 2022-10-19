<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 创建数据模型
 * @OA\Schema(
 *     title="AlarmGrade model",
 *     description="AlarmGrade model",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="description",
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
 *         property="orgnization_id",
 *         type="integer"
 *     ),
 * )
 */
class AlarmGrade extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'alarm_grade';
    protected $fillable = ['name', 'min_value', 'max_value', 'description', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
