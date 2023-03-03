<?php

namespace App\Http\Requests\MIS;

use Illuminate\Foundation\Http\FormRequest;

class ClassSchduleRequest extends FormRequest
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
            'date_type' => 'required',
            'class_type' => 'required',
            'date' => 'required'
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
            'date_type.required' => '日期类型不能为空',
            'class_type.required' => '排班类型不能为空',
            'date.required' => '排班起始日期不能为空'
        ];
    }
}
