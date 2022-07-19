<?php

namespace App\Http\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Log;
/**
 * App\Http\Models\SIS\GrabGarbage
 * author 叶文华
 * IEC104 恩倍力垃圾抓斗
 */
class GrabGarbage extends Model
{
    protected $table = 'grab_garbage_yongqiang2';
    protected $fillable = ['allsn', 'sn', 'time', 'che', 'dou', 'liao', 'code', 'lost', 'hev'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function insertOne($params){
        return self::create($params);
    }

    public function insertMany($params){
        return self::insert($params);
    }

    public function findRowBySn($sn){
        return self::where('sn', $sn)->first();
    }
}

/**
 * @SWG\Definition(
 *     definition="GrabGarbage",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="allsn",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="sn",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="time",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="che",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="dou",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="liao",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="code",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="lost",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="hev",
 *         type="integer"
 *     ),
 * )
 */
