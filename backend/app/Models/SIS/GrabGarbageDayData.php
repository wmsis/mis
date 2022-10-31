<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrabGarbageDayData extends Model
{
    use HasFactory;
    protected $table = 'grab_garbage_day_data_yongqiang2';
    protected $fillable = ['liao', 'value', 'date'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
