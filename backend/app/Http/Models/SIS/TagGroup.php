<?php


namespace App\Http\Models\SIS;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Http\Models\SIS\TagGroup
 *
 * @property int $id
 * @property string $name 组名
 * @property string|null $description 描述
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\SIS\HistorianTag[] $historian_tags
 * @property-read int|null $historian_tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\TagGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagGroup whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\TagGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\TagGroup withoutTrashed()
 * @mixin \Eloquent
 */
class TagGroup extends Model
{
    use SoftDeletes;

    protected $table = 'tag_group';
    protected $fillable = ['name', 'description'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function historian_tags()
    {
        return $this->hasMany('App\Http\Models\SIS\HistorianTag');
    }
}

/**
 * @SWG\Definition(
 *     definition="TagGroup",
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
 *     ),
 * )
 */
