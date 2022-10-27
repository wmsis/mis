<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlarmRecord extends Model
{
    use HasFactory;
    protected $table = 'alarm_record';
    protected $fillable = ['alarm_rule_id', 'min_value', 'max_value', 'start_time', 'end_time', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
