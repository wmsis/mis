<?php

namespace App\Models\Factory;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Factory\GrabGarbage
 * author 叶文华
 * IEC104 恩倍力垃圾抓斗
 */
class GrabGarbage extends Model
{
    protected $connection = 'garbage';
    protected $table = 'log';
    //protected $primaryKey = 'allsn';
    protected $fillable = ['allsn', 'sn', 'time', 'che', 'dou', 'liao', 'code', 'lost', 'hev'];

    // protected $connection = 'likeshop';
    // protected $table = 'ls_admin';
    // protected $fillable = ['name', 'account'];

    public function findByDate($date){
        $begin = strtotime($date.' 00:00:00');
        $end = strtotime($date.' 23:59:59');
        $rows = self::where('time', '>=', $begin)
            ->where('time', '<=', $end)
            ->get();

        return $rows;
    }
}
