<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ElectricityMapStoreRequest extends FormRequest
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
            'addr' => 'required',
            'cn_name' => 'required',
            'func' => 'required',
            'rate' => 'required',
            'orgnization_id' => 'required',
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
            'addr.required' => '点位地址不能为空',
            'cn_name.required' => '中文名称不能为空',
            'func.required' => '系数不能为空',
            'rate.required' => '倍率不能为空',
            'orgnization_id.required' => '组织ID不能为空',
        ];
    }
}
