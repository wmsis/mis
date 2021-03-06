<?php

namespace App\Http\Models\Factory;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Http\Models\Factory\GrabGarbage
 * author 叶文华
 * IEC104 恩倍力垃圾抓斗
 */
class GrabGarbage extends Model
{
    protected $connection = 'mysql_yongqiang2_grab_garbage';  //连接名
    protected $table = 'log';
    protected $primaryKey = 'allsn';
    protected $fillable = ['allsn', 'sn', 'time', 'che', 'dou', 'liao', 'code', 'lost', 'hev'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function findByDate($date){
        $begin = strtotime($date.' 00:00:00');
        $end = strtotime($date.' 23:59:59');
        $rows = self::where('time', '>=', $begin)
            ->where('time', '<=', $end)
            ->get();

        return $rows;
    }
}
