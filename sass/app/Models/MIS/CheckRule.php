<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\CheckRuleGroup;
use App\Models\MIS\CheckRuleAllocation;

class CheckRule extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'check_rule';
    protected $fillable = ['orgnization_id', 'name', 'value', 'remark', 'dcs_standard_ids', 'type', 'check_rule_group_id', ' isopen'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function group()
    {
        return $this->belongsTo(CheckRuleGroup::class);
    }

    public function allocation()
    {
        return $this->hasMany(CheckRuleAllocation::class);
    }
}
