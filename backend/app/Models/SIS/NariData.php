<?php

namespace App\Models\SIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NariData extends Model
{
    use HasFactory;
    protected $table = 'nari_yongqiang1';
    protected $fillable = ['address', 'value', 'quality', 'flag'];

    public function findById($id){
        return self::find($id);
    }

    public function insertOne($params){
        return self::create($params);
    }

    public function insertMany($params){
        return self::insert($params);
    }

    public function findNotComputer(){
        return self::where("flag", 0)->limit(1000)->get();
    }

    public function updateByIds($params, $ids){
        return self::whereIn("id", $ids)->update($params);
    }

}
