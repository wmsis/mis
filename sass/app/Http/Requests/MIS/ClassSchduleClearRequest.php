<?php

namespace App\Http\Requests\MIS;

use Illuminate\Foundation\Http\FormRequest;

class ClassSchduleClearRequest extends FormRequest
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
            'class_type' => 'required',
            'start' => 'required',
            'end' => 'required'
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
            'class_type.required' => '排班类型不能为空',
            'start.required' => '排班起始日期不能为空',
            'end.required' => '排班截止日期不能为空'
        ];
    }
}
