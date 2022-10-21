<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SIS\DcsGroup;

/**
 * 创建数据模型
 * @OA\Schema(
 *     title="DcsStandard model",
 *     description="DcsStandard model",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="en_name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="cn_name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="dcs_group_id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="messure",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="sort",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="is_show",
 *         type="integer"
 *     ),
 * )
 */
class DcsStandard extends Model
{
    use softDeletes;
    protected $table = 'dcs_standard';
    protected $fillable = ['en_name', 'cn_name', 'dcs_group_id', 'type', 'messure', 'sort', 'is_show'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    /**
     * 获取拥有此标准DCS名称的分组
     */
    public function dcsGroup()
    {
        return $this->belongsTo(DcsGroup::class);
    }
}
