<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'isopen' => 'required',
            'mobile' => 'required',
        ];
    }

    /**
     * 自定义错误消息
     */
    public function messages(){
        return [
            'name.required' => '用户名不能为空',
            'isopen.required' => '是否开启不能为空',
            'mobile.required' => '手机号码不能为空'
        ];
    }
}
