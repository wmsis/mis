<?php

namespace App\Services;
use Illuminate\Support\Facades\Cache;

class CacheService{
    public function getCache($key){
        $data = null;
        if ($key && Cache::has($key)) {
            //有缓存
            $data = Cache::get($key);
        }
        return $data;
    }

    public function setCache($key, $data, $expire){
        $this->clearCache($key);
        if($key && $data && $expire) {
            Cache::add($key, $data, $expire);
        }
    }

    public function clearCache($key){
        if($key && Cache::has($key)) {
            Cache::forget($key);
        }
    }
}