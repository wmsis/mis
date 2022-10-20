<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\DevicePropertyTemplate;

/**
 * 创建数据模型
 * @OA\Schema(
 *     title="DeviceTemplate model",
 *     description="DeviceTemplate model",
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
 *     @OA\Property(
 *         property="orgnization_id",
 *         type="integer"
 *     ),
 * )
 */
class DeviceTemplate extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'device_template';
    protected $fillable = ['name', 'parent_id', 'ancestor_id', 'level', 'sort',  'is_group', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function device_property_templates()
    {
        return $this->hasMany(DevicePropertyTemplate::class);
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
