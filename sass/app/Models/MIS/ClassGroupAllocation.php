<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\CheckRuleAllocationDetail;

class ClassGroupAllocation extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'class_group_allocation';
    protected $fillable = ['orgnization_id', 'class_group_name', 'isopen'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function detail()
    {
        return $this->hasMany(CheckRuleAllocationDetail::class);
    }
}
