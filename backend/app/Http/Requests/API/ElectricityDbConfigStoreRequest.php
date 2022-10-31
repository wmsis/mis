<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ElectricityDbConfigStoreRequest extends FormRequest
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
            'master_ip' => 'required',
            //'slave_ip' => 'required',
            'common_addr' => 'required',
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
            'master_ip.required' => '主站IP不能为空',
            //'slave_ip.required' => '密码不能为空',
            'common_addr.required' => '公共地址不能为空',
            'orgnization_id.required' => '组织ID不能为空',
        ];
    }
}
