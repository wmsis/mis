<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\ClassLoop;

class ClassLoopDetail extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'class_loop_detail';
    protected $fillable = ['class_loop_id', 'class_define_name', 'sort', 'class_define_time'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function classloop()
    {
        return $this->belongsTo(ClassLoop::class);
    }
}
