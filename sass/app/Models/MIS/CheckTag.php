<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SIS\Orgnization;
use App\Models\SIS\DcsStandard;

class CheckTag extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'check_tag';
    protected $fillable = ['orgnization_id', 'dcs_standard_id', 'remark', 'point_every_alarm', 'user_ids'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function dcsStandard()
    {
        return $this->belongsTo(DcsStandard::class);
    }

    public function orgnization()
    {
        return $this->belongsTo(Orgnization::class);
    }
}
