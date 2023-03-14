<?php

namespace App\Repositories;

use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use App\Models\MIS\ClassSchdule;

/**
 * Class ClassSchduleRepository.
 */
class ClassSchduleRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return ClassSchdule::class;
    }

    //按人删除排班
    public function deleteByUser($params)
    {
        $res = $this;
        $flag = false;
        if(isset($params['start']) && $params['start'] && isset($params['end']) && $params['end']){
            $res = $res->where('date', $params['start'], '>=')->where('date', $params['end'], '<=');
            $flag = true;
        }
        if(isset($params['orgnization_id']) && $params['orgnization_id']){
            $res = $res->where('orgnization_id', $params['orgnization_id']);
            $flag = true;
        }
        if(isset($params['user_id']) && $params['user_id']){
            $res = $res->where('user_id', $params['user_id']);
            $flag = true;
        }
        $res = $flag ? $res->forceDelete() : null;

        return $res;
    }

    //按班组删除排班
    public function deleteByGroup($params)
    {
        $res = $this;
        $flag = false;
        if(isset($params['start']) && $params['start'] && isset($params['end']) && $params['end']){
            $res = $res->where('date', $params['start'], '>=')->where('date', $params['end'], '<=');
            $flag = true;
        }
        if(isset($params['orgnization_id']) && $params['orgnization_id']){
            $res = $res->where('orgnization_id', $params['orgnization_id']);
            $flag = true;
        }
        if(isset($params['class_group_name']) && $params['class_group_name']){
            $res = $res->where('class_group_name', $params['class_group_name']);
            $flag = true;
        }
        $res = $flag ? $res->forceDelete() : null;

        return $res;
    }
}
