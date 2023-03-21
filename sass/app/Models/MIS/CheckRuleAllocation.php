<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\CheckRule;

class CheckRuleAllocation extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'check_rule_allocation';
    protected $fillable = ['check_rule_id', 'job_station_id', 'percent', 'isopen'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function checkRule()
    {
        return $this->belongsTo(CheckRule::class);
    }
}
