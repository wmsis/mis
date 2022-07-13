<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BatchDeleteRequest extends FormRequest
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
            'idstring' => 'required',
            'password' => 'required'
        ];
    }

    /**
     * 自定义错误消息
     */
    public function messages(){
        return [
            'idstring.required' => 'id列表不能为空',
            'password.required' => '登录密码不能为空'
        ];
    }
}
