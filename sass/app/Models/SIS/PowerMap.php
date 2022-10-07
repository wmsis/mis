<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PowerMap extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'power_map';
    protected $fillable = ['electricity_map_ids', 'dcs_standard_id', 'func', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
