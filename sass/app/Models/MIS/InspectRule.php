<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\DeviceProperty;
use App\Models\MIS\Task;

/**
 * 创建数据模型
 * @OA\Schema(
 *     title="InspectRule model",
 *     description="InspectRule model",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="device_property_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string"
 *     ),
 * )
 */
class InspectRule extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'inspect_rule';
    protected $fillable = ['name', 'device_property_id', 'content'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function device_property(){
        return $this->belongsTo(DeviceProperty::class);
    }

    public function tasks(){
        return $this->belongsToMany(Task::class, 'task_inspect_rule', 'inspect_rule_id', 'task_id')->withTimestamps();
    }
}
