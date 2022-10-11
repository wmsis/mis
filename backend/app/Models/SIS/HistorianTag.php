<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Log;
use MyCacheService;

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
        $this->historian_tag_all = md5('historian_tag_all' . $this->table);
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
        $key = md5('historian_tag_' . $id . $this->table);
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
            $key_single = md5('historian_tag_' . $params['id'] . $this->table);
            $this->clearCache($key_single);
        }
        if(isset($params['ids']) && $params['ids']){
            $tagsIdList = explode(',', $params['ids']);
            foreach($tagsIdList as $id){
                $key_single = md5('historian_tag_' . $id . $this->table);
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
        return MyCacheService::getCache($key);
    }

    private function setCache($key, $data, $expire){
        MyCacheService::setCache($key, $data, $expire);
    }

    private function clearCache($key){
        MyCacheService::clearCache($key);
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
