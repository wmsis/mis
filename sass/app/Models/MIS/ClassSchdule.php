<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSchdule extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'class_schdule';
    protected $fillable = ['orgnization_id', 'user_id', 'date', 'class_define_name', 'start', 'end', 'class_group_name'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
