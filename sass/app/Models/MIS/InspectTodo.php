<?php

namespace App\Models\MIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MIS\InspectLine;
use App\Models\User;

class InspectTodo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inspect_todo';
    protected $fillable = ['inspect_line_id', 'period', 'start', 'end', 'user_id', 'status', 'remark', 'sort', 'orgnization_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];


    public function inspectLine()
    {
        return $this->belongsTo(InspectLine::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
