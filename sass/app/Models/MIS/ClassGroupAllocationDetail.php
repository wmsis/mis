<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassGroupAllocationDetail extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'class_group_allocation_detail';
    protected $fillable = ['class_group_allocation_id', 'job_station_id', 'percent'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
