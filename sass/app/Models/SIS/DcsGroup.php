<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SIS\DcsStandard;

/**
 * 创建数据模型
 * @OA\Schema(
 *     title="DcsGroup model",
 *     description="DcsGroup model",
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
 * )
 */
class DcsGroup extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'dcs_group';
    protected $fillable = ['name', 'description'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    /**
     * 获取与分组相关的标准DCS名称记录
     */
    public function dcsStandard()
    {
        return $this->hasMany(DcsStandard::class);
    }
}
