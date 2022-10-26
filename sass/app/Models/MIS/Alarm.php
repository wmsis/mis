<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\AlarmRule;

class Alarm extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'alarm';
    protected $fillable = ['alarm_rule_id', 'content', 'confirm_time', 'status', 'orgnization_id', 'remark'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function alarm_rule()
    {
        return $this->belongsTo(AlarmRule::class);
    }
}
