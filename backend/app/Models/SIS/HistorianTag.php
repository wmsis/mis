<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Log;
use CacheService;

/**
 * App\Models\SIS\HistorianTag
 *
 * @property int $id
 * @property string $tag_id tag id
 * @property string $tag_name historian中的标签名
 * @property string|null $description
 * @property string|null $alias 别名
 * @property string|null $measure 量程
 * @property float|null $upper_limit 上限
 * @property float|null $lower_limit 下限
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SIS\HistorianTag onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag whereLowerLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag whereMeasure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag whereTagName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag whereUpperLimit($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SIS\HistorianTag withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SIS\HistorianTag withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @property float|null $origin_upper_limit 原始上限值, historian记录的量程上限
 * @property float|null $origin_lower_limit 原始下限值, historian记录的量程下限
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag whereOriginLowerLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SIS\HistorianTag whereOriginUpperLimit($value)
 */
class HistorianTag extends Model
{
    use SoftDeletes;

    protected $table = 'historian_tag_yongqiang2';
    protected $fillable = ['tag_id', 'tag_name', 'description', 'alias', 'measure', 'upper_limit', 'lower_limit',
        'origin_upper_limit', 'origin_lower_limit'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public $historian_tag_all;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->historian_tag_all = md5('historian_tag_all');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'tag_user', 'tag_id', 'user_id')->withTimestamps();
    }

    public function findAll(){
        $key = $this->historian_tag_all;
        $data = $this->getCache($key);
        if (!$data) {
            //没有缓存
            $data = $this->all();
            $this->setCache($key, $data, 8 * 3600);
        }
        return $data;
    }

    public function findByPage($params){
        $tags = $this->select(['*']);
        if (isset($params['alias']) && $params['alias']) {
            $tags = $tags->where('alias', 'like', "%{$params['alias']}%");
        }
        if (isset($params['tag_name']) && $params['tag_name']) {
            $tags = $tags->where('tag_name', 'like', "%{$params['tag_name']}%");
        }
        $total = $tags->count();
        $tags = $tags->offset($params['offset'])->limit($params['limit'])->get();
        $data = array(
            "total" => $total,
            "data" => $tags
        );

        return $data;
    }

    public function findByID($id){
        $key = md5('historian_tag_' . $id);
        $data = $this->getCache($key);
        if (!$data) {
            //没有缓存
            $data = $this->find($id);
            $this->setCache($key, $data, 8 * 3600);
        }
        return $data;
    }

    public function updateCache($params){
        if(isset($params['id']) && $params['id']){
            $key_single = md5('historian_tag_' . $params['id']);
            $this->clearCache($key_single);
        }
        if(isset($params['ids']) && $params['ids']){
            $tagsIdList = explode(',', $params['ids']);
            foreach($tagsIdList as $id){
                $key_single = md5('historian_tag_' . $id);
                $this->clearCache($key_single);
            }
        }
        $this->clearCache($this->historian_tag_all);
    }

    /*
     * @param $name=array
     */
    public function findByTagnames($names){
        return $this->whereIn('tag_name', $names)->get();
    }

    private function getCache($key){
        return CacheService::getCache($key);
    }

    private function setCache($key, $data, $expire){
        CacheService::setCache($key, $data, $expire);
    }

    private function clearCache($key){
        CacheService::clearCache($key);
    }
}

/**
 * @OA\Definition(
 *     definition="HistorianTag",
 *     type="object",
 *     required={"tag_id", "tag_name"},
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="tag_id",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="tag_name",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="alias",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="measure",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="upper_limit",
 *         type="number"
 *     ),
 *     @OA\Property(
 *         property="lower_limit",
 *         type="number"
 *     ),
 *     @OA\Property(
 *         property="origin_upper_limit",
 *         type="number"
 *     ),
 *     @OA\Property(
 *         property="origin_lower_limit",
 *         type="number"
 *     ),
 * )
 */
