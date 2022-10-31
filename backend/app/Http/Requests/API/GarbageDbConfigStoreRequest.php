<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class GarbageDbConfigStoreRequest extends FormRequest
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
            'type' => 'required',
            'user' => 'required',
            'password' => 'required',
            'ip' => 'required',
            'port' => 'required',
            'orgnization_id' => 'required',
            'db_name' => 'required',
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
            'user.required' => '用户名不能为空',
            'password.required' => '密码不能为空',
            'ip.required' => 'IP不能为空',
            'port.required' => '端口不能为空',
            'type.required' => '数据库类型不能为空',
            'orgnization_id.required' => '组织ID不能为空',
            'db_name.required' => '数据库名不能为空',
        ];
    }
}
