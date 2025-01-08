<?php

namespace App\Repositories;

use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use App\Models\MIS\InspectPoint;
use Illuminate\Database\QueryException;
use Log;

/**
 * Class InspectPointRepository.
 */
class InspectPointRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return InspectPoint::class;
    }

    public function page($num, $page, $orgnization_id)
    {
        $data = $this->model->where('orgnization_id', $orgnization_id)->paginate($num, ['*'], 'page', $page);
        Log::info("0000000000000");
        Log::info(var_export($data, true));
        foreach ($data->data as $key => $item) {
            $device = $item->device;
        }

        return array("data"=>$data->data, "total"=>$data->total);
    }

    public function store($params)
    {
        $input = array();
        $fillable = ['device_id', 'address', 'remark', 'sort', 'orgnization_id'];
        foreach ($params as $key => $value) {
            if(in_array($key, $fillable)){
                $input[$key] = $value;
            }
        }

        $res = null;
        try {
            $res = $this->model->create($input);
        } catch (QueryException $e) {
            $res =  $e->getMessage();
        }

        return $res;
    }

    public function show($id)
    {
        return $this->model->find($id);
    }

    public function update($input, $id)
    {
        $data = $this->model->find($id);
        $allowField = ['device_id', 'address', 'remark', 'sort', 'orgnization_id'];
        foreach ($allowField as $field) {
            if (key_exists($field, $input)) {
                $inputValue = $input[$field];
                $data[$field] = $inputValue;
            }
        }
        try {
            $data->save();
            $data->refresh();
            $res = $data;
        } catch (Exception $ex) {
            $res = $ex->getMessage();
        }

        return $res;
    }

    public function destroy($id)
    {
        $data = $this->model->find($id);
        try {
            $data->delete();
            $res = '';
        } catch (Exception $e) {
            $res = $ex->getMessage();
        }

        return $res;
    }
}
