<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Log;

/**
 * 创建数据模型
 * @OA\Schema(
 *     title="WeighBridgeFormat model",
 *     description="WeighBridgeFormat model",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="grossdatetime",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="taredatetime",
 *         type="string"
 *     ),
 *     @OA\Property(
 *         property="net",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="weighid",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="weighbridge_cate_small_id",
 *         type="integer"
 *     ),
 * )
 */
class WeighBridgeFormat extends Model
{
    protected $table = 'weighbridge_format_yongqiang2';
    protected $fillable = ['grossdatetime', 'taredatetime', 'net', 'weighid', 'weighbridge_cate_small_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function destroyByWeighId($id){
        return self::where('weighid', $id)->delete();
    }

    public function insertMany($params){
        return self::insert($params);
    }

    public function updateOne($params, $where){
        $i = 0;
        $obj = null;
        foreach ($where as $key => $value) {
            if($i == 0){
                $obj = self::where($key, $value);
            }
            else{
                $obj = $obj->where($key, $value);
            }
            $i++;
        }
        return $obj->update($params);
    }
}
