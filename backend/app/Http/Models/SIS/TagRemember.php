<?php

namespace  App\Http\Models\SIS;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Http\Models\SIS\TagRemember
 *
 * @property int $id
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property string $path path路径
 * @property int $user_id user id
 * @property string $tag_ids historian tag id(主键)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagRemember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagRemember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagRemember query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagRemember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagRemember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagRemember wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagRemember whereTagIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagRemember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\TagRemember whereUserId($value)
 * @mixin \Eloquent
 */
class TagRemember extends Model
{
    protected $table = 'tag_remember';
    protected $fillable = ['path', 'user_id', 'tag_ids'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
