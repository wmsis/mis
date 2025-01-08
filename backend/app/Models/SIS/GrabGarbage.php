<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Model;
use Log;
/**
 * App\Models\SIS\GrabGarbage
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

    public function findLatestReport(){
        return self::orderBy("time", "DESC")->first();
    }
}

/**
 * @OA\Definition(
 *     definition="GrabGarbage",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="allsn",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="sn",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="time",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="che",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="dou",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="liao",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="code",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="lost",
 *         type="integer"
 *     ),
 *     @OA\Property(
 *         property="hev",
 *         type="integer"
 *     ),
 * )
 */
