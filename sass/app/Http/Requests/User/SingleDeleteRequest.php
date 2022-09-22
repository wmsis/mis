<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class SingleDeleteRequest extends FormRequest
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
            'id' => 'required'
        ];
    }

    /**
     * 自定义错误消息
     */
    public function messages(){
        return [
            'id.required' => '用户id不能为空'
        ];
    }
}
