<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orgnization extends Model
{
    use softDeletes, HasFactory;
    protected $connection = 'mysql_mis';
    protected $table = 'orgnization';
    protected $fillable = ['name', 'description', 'parent_id', 'level', 'sort', 'tenement_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function childs() {
        return $this->hasMany('App\Models\System\Orgnization', 'parent_id');
    }

    public function parent() {
        return $this->belongsTo('App\Models\System\Orgnization', 'parent_id');
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
