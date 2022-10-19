<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\Alarm;
use App\Models\MIS\Announcement;
use App\Models\Users;

class Notice extends Model
{
    use HasFactory, softDeletes;
    protected $table = 'notice';
    protected $fillable = ['user_id', 'status', 'confirm_time', 'type', 'foreign_id', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }
}
