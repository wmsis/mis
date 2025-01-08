<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\InspectLine;

class InspectPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inspect_plan';
    protected $fillable = ['inspect_line_id', 'period', 'start', 'end', 'user_id', 'status', 'remark', 'sort', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];


    public function inspectLine()
    {
        return $this->belongsTo(InspectLine::class);
    }
}
