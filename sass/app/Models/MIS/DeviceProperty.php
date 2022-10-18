<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\Device;

/**
 * 创建数据模型
 * @OA\Schema(
 *     title="DeviceProperty model",
 *     description="DeviceProperty model",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="value",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="device_id",
 *         type="integer"
 *     ),
 * )
 */
class DeviceProperty extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'device_property';
    protected $fillable = ['name', 'value', 'device_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    /**
     * 获取拥有此属性的设备
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
