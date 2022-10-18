<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\Device;
use App\Models\SIS\DcsStandard;
use App\Models\MIS\AlarmGrade;

class AlarmRule extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'alarm_rule';
    protected $fillable = ['name', 'device_id', 'dcs_standard_id', 'period', 'sustain', 'min_value', 'max_value', 'alarm_grade_id', 'type', 'notify_user_ids'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function dcs_standard()
    {
        return $this->belongsTo(DcsStandard::class);
    }

    public function alarm_grade()
    {
        return $this->belongsTo(AlarmGrade::class);
    }
}
