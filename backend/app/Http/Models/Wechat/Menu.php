<?php

namespace App\Http\Models\Wechat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  //添加软删除

/**
 * App\Http\Models\Wechat\Menu
 *
 * @property int $id
 * @property string|null $path 路径
 * @property string|null $name 名称
 * @property int|null $level 层级
 * @property bool|null $is_root 是否根节点
 * @property int|null $sort 排序号
 * @property bool|null $is_open 是否开启
 * @property string|null $type
 * @property string|null $keyword
 * @property string|null $appid
 * @property string|null $pagepath
 * @property string|null $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\Menu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereAppid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereIsOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereIsRoot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu wherePagepath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\Menu withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\Menu withoutTrashed()
 * @mixin \Eloquent
 */
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
 * @SWG\Definition(
 *     definition="Menu",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="path",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="level",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="is_root",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="sort",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="is_open",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="type",
 *         type="string",
 *         enum={"view", "miniprogram", "click"}
 *     ),
 *     @SWG\Property(
 *         property="keyword",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="appid",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="pagepath",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="url",
 *         type="string"
 *     )
 * )
 */
