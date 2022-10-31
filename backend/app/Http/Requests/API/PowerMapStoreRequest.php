<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class PowerMapStoreRequest extends FormRequest
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
            'electricity_map_ids' => 'required',
            'dcs_standard_id' => 'required',
            'func' => 'required',
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
            'electricity_map_ids.required' => '电表映射ID不能为空',
            'dcs_standard_id.required' => '统一字段名ID不能为空',
            'func.required' => '函数不能为空',
            'orgnization_id.required' => '组织ID不能为空',
        ];
    }
}
