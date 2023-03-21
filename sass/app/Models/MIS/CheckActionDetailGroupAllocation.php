<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckActionDetailGroupAllocation extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'check_action_detail_group_allocation';
    protected $fillable = ['orgnization_id', 'class_group_name', 'check_action_detail_id', 'job_station_id', 'user_id', 'value', 'percent'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
