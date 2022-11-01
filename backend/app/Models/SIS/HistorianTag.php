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

    protected $fillable = ['tag_id', 'tag_name'];

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
 *     required={"tag_name"},
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="tag_name",
 *         type="string"
 *     ),
 * )
 */
