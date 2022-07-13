<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UserRegistRequest extends FormRequest
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
            'mobile' => 'required',
            'password' => 'required',
            'name' => 'required',
        ];
    }

    /**
     * 自定义错误消息
     */
    public function messages(){
        return [
            'mobile.required' => '手机号码不能为空',
            'password.required'  => '密码不能为空',
            'name.required'  => '用户名不能为空'
        ];
    }
}
