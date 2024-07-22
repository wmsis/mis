<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\DeviceTemplate;

/**
 * 创建数据模型
 * @OA\Schema(
 *     title="DevicePropertyTemplate model",
 *     description="DevicePropertyTemplate model",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="device_template_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="value",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="default_value",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="orgnization_id",
 *         type="integer"
 *     )
 * )
 */
class DevicePropertyTemplate extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'device_property_template';
    protected $fillable = ['device_template_id', 'name', 'type', 'value', 'default_value', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function device_template()
    {
        return $this->belongsTo(DeviceTemplate::class);
    }
}
