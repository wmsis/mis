<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckTagDetail extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'check_tag_detail';
    protected $fillable = ['user_id', 'date', 'first_alarm_num', 'second_alarm_num', 'class_alarm_num'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
