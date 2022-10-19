<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\Device;
use App\Models\Users;
use App\Models\MIS\InspectRule;

/**
 * 创建数据模型
 * @OA\Schema(
 *     title="Task model",
 *     description="Task model",
 *     @OA\Property(
 *         property="id",
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
 *         property="begin",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="end",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="device_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="confirm_time",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="remark",
 *         type="string"
 *     ),
 * )
 */
class Task extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'task';
    protected $fillable = ['name', 'type', 'begin', 'end', 'user_id', 'device_id', 'content', 'confirm_time', 'status', 'remark'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function user()
    {
        return $this->belongsTo(Users::class);
    }

    public function inspect_rules(){
        return $this->belongsToMany(InspectRule::class, 'task_inspect_rule', 'task_id', 'inspect_rule_id')->withTimestamps();
    }
}
