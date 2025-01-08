<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\Device;
use App\Models\MIS\InspectLine;

/**
 * 创建数据模型
 * @OA\Schema(
 *     title="InspectPoint model",
 *     description="InspectPoint model",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="device_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="remark",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="sort",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="orgnization_id",
 *         type="integer"
 *     ),
 * )
 */
class InspectPoint extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inspect_point';
    protected $fillable = ['device_id', 'address', 'remark', 'sort', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function inspectLines()
    {
        return $this->hasMany(InspectLine::class);
    }
}
