<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class API extends Model
{
    use softDeletes;
    protected $table = 'api';
    protected $fillable = ['name', 'description', 'parent_id', 'level', 'sort', 'url'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    //权限属于哪个角色
    public function roles(){
        return $this->belongsToMany('App\Models\Role', 'role_api', 'api_id', 'role_id')->withTimestamps();
    }

    public function roots()
    {
        return $this->whereNull('deleted_at')
            ->where('level', 1)
            ->orderBy('sort', 'asc')
            ->get();
    }

    public function children($parent_id)
    {
        return $this->whereNull('deleted_at')
            ->where('parent_id', $parent_id)
            ->orderBy('sort', 'asc')
            ->get();
    }
}
