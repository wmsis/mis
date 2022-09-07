<?php

namespace App\Models\Wechat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除


class Menu extends Model
{
    use SoftDeletes;
    //站点
    protected  $table = 'wx_menu';
    protected $fillable = ['path', 'name', 'level', 'is_root', 'sort', 'is_open', 'type', 'keyword', 'appid', 'pagepath', 'url']; //批量赋值

    //属性转换
    protected $casts = [
        'is_open' => 'boolean',  //将其由 integer 类型（0或1）转换为boolean 类型
        'is_root' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function lists()
    {
        return $this->whereNull('deleted_at')
            ->where('is_open', 1)
            ->orderBy('sort', 'asc')
            ->get();
    }

    public function keywords()
    {
        return $this->whereNull('deleted_at')
            ->where('is_open', 1)
            ->where('type', 'click')
            ->orderBy('sort', 'asc')
            ->get();
    }

    public function topmenu()
    {
        return $this->whereNull('deleted_at')
            ->where('is_open', 1)
            ->where('level', 1)
            ->orderBy('sort', 'asc')
            ->offset(0)
            ->limit(3)
            ->get();
    }

    public function insert($params)
    {
        return $this->insertGetId($params);
    }

    public function rowById($id){
        return $this->select('*')->where('id', $id)->first();
    }

    public function childmenu($path)
    {
        $key = $path.'/%';
        return $this->whereNull('deleted_at')
            ->where('is_open', 1)
            ->where('level', 2)
            ->where('path','like', $key)
            ->orderBy('sort', 'asc')
            ->get();
    }
}

/**
 * @OA\Definition(
 *     definition="Menu",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="path",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="level",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="is_root",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="sort",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="is_open",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         enum={"view", "miniprogram", "click"}
 *     ),
 *     @OA\Property(
 *         property="keyword",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="appid",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="pagepath",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="url",
 *         type="string"
 *     )
 * )
 */
