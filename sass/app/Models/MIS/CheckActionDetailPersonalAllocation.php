<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckActionDetailPersonalAllocation extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'check_action_detail_personal_allocation';
    protected $fillable = ['check_action_detail_id', 'user_id', 'percent', 'value'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
