<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckPointDetail extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'check_point_detail';
    protected $fillable = ['orgnization_id', 'user_id', 'class_group_name', 'date', 'value', 'reason', 'type', ' foreign_key'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
