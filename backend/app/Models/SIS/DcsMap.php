<?php
/**
 * App\Models\SIS\DcsMap
 * author 叶文华
 * DCS 标准名称
 */
namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DcsMap extends Model
{
    use softDeletes;
    protected $table = 'dcs_map';
    protected $fillable = ['tag_ids', 'en_name', 'cn_name', 'func', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

}
