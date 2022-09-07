<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DcsStandard extends Model
{
    use softDeletes;
    protected $table = 'dcs_standard';
    protected $fillable = ['en_name', 'cn_name'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}

/**
 * @OA\Definition(
 *     definition="DcsStandard",
 *     type="object",
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
 * )
 */
