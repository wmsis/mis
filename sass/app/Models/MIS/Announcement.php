<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 创建数据模型
 * @OA\Schema(
 *     title="Announcement model",
 *     description="Announcement model",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="notify_user_ids",
 *         type="string"
 *     ),
 * )
 */
class Announcement extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'announcement';
    protected $fillable = ['title', 'content', 'notify_user_ids'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
