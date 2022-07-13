<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreElectricityRequest extends FormRequest
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
            'address' => 'required',
            'value' => 'required',
            'actual_value' => 'required',
            'quality' => 'required',
            'factor' => 'required',
            'cn_name' => 'required'
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
            'address.required' => '地址不能为空',
            'value.required' => '数据值不能为空',
            'actual_value.required' => '实际值不能为空',
            'quality.required' => '品质描述不能为空',
            'factor.required' => '系数不能为空',
            'cn_name.required' => '中文名称不能为空'
        ];
    }
}
