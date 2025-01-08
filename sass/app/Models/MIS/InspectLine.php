<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\InspectPoint;

class InspectLine extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inspect_line';
    protected $fillable = ['remark', 'sort', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function inspectPoints()
    {
        return $this->hasMany(InspectPoint::class);
    }
}
