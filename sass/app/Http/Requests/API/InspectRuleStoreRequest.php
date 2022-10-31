<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class InspectRuleStoreRequest extends FormRequest
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
            'device_property_id' => 'required',
            'content' => 'required',
            'standard' => 'required',
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
            'name.required' => '巡检规则名称不能为空',
            'device_property_id.required' => '设备属性ID不能为空',
            'content.required' => '巡检内容不能为空',
            'standard.required' => '巡检标准规范不能为空',
        ];
    }
}
