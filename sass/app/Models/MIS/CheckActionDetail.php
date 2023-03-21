<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckActionDetail extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'check_action_detail';
    protected $fillable = ['orgnization_id', 'check_rule_id', 'value', 'type', 'date'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
