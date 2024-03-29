<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\ClassLoopDetail;

class ClassLoop extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'class_loop';
    protected $fillable = ['orgnization_id', 'name', 'loop_days'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function detail()
    {
        return $this->hasMany(ClassLoopDetail::class);
    }
}
