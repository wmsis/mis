<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricityDayData extends Model
{
    use HasFactory;
    protected $table = 'electricity_day_data_yongqiang2';
    protected $fillable = ['electricity_map_id', 'value', 'date'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
