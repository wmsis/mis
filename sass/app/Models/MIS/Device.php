<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\DeviceProperty;
use App\Models\MIS\Task;
use App\Models\MIS\AlarmRule;

/**
 * 创建数据模型
 * @OA\Schema(
 *     title="Device model",
 *     description="Device model",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="ancestor_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="orgnization_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="quality_date",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="factory_date",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="code",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="img",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="level",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="sort",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="is_group",
 *         type="integer"
 *     ),
 * )
 */
class Device extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'device';
    protected $fillable = ['name', 'parent_id', 'ancestor_id', 'orgnization_id', 'level', 'sort', 'quality_date', 'factory_date', 'code', 'img', 'is_group'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    /**
     * 获取设备自定义属性
     */
    public function device_properties()
    {
        return $this->hasMany(DeviceProperty::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function alarm_rules()
    {
        return $this->hasMany(AlarmRule::class);
    }

    public function roots($orgnization_id)
    {
        return $this->whereNull('deleted_at')
            ->where('orgnization_id', $orgnization_id)
            ->where('level', 1)
            ->orderBy('sort', 'asc')
            ->get();
    }

    public function children($parent_id)
    {
        return $this->whereNull('deleted_at')
            ->where('parent_id', $parent_id)
            ->orderBy('sort', 'asc')
            ->get();
    }
}
