<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class TakemoneyCheckRequest extends FormRequest
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
            'id' => 'required',
            'status' => 'required'
        ];
    }

    /**
     * 自定义错误消息
     */
    public function messages(){
        return [
            'id.required' => 'id不能为空',
            'status.required' => '审核状态不能为空'
        ];
    }
}
