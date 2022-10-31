<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class DcsStandardStoreRequest extends FormRequest
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
            'cn_name' => 'required',
            'en_name' => 'required',
            'type' => 'required',
            'messure' => 'required',
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
            'cn_name.required' => '中文名称不能为空',
            'en_name.required' => '英文名称不能为空',
            'type.required' => '类型不能为空',
            'messure.required' => '单位量程不能为空',
        ];
    }
}
