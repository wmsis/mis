<?php

namespace App\Http\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Http\Models\SIS\HistorianModule
 *
 * @property int $id
 * @property string $name 模块名
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\SIS\HistorianTag[] $historian_tags
 * @property-read int|null $historian_tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\HistorianModule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\HistorianModule withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\HistorianModule withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $description
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule whereDescription($value)
 */
class HistorianModule extends Model
{
    use softDeletes;

    protected $table = 'historian_module';
    protected $fillable = ['name', 'description'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    /**
     * 获取 historian_module 的所有 historian_tag
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function historian_tags()
    {
        return $this->hasMany('App\Http\Models\SIS\HistorianTag');
    }
}

/**
 * @SWG\Definition(
 *     definition="HistorianModule",
 *     type="object",
 *     required={"name"},
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="description",
 *         type="string"
 *     )
 * )
 */

