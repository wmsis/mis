<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'type' => 'required',
            'begin' => 'required',
            'end' => 'required',
            'user_id' => 'required',
            'device_id' => 'required',
            'content' => 'required',
        ];
    }

    /**
     * 自定义错误信息
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => '任务名称不能为空',
            'type.required' => '任务类型不能为空',
            'begin.required' => '开始时间不能为空',
            'end.required' => '结束时间不能为空',
            'user_id.required' => '用户ID不能为空',
            'device_id.required' => '设备ID不能为空',
            'content.required' => '任务内容不能为空',
        ];
    }
}
