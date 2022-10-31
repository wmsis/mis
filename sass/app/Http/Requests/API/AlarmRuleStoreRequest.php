<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class AlarmRuleStoreRequest extends FormRequest
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
            'device_id' => 'required',
            'dcs_standard_id' => 'required',
            'period' => 'required',
            'sustain' => 'required',
            'min_value' => 'required',
            'max_value' => 'required',
            'alarm_grade_id' => 'required',
            'notify_user_ids' => 'required',
            'type' => 'required',
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
            'name.required' => '报警规则名称不能为空',
            'device_id.required' => '设备ID不能为空',
            'dcs_standard_id.required' => '统一字段名称ID不能为空',
            'period.required' => '报警周期不能为空',
            'sustain.required' => '持续报警周期不能为空',
            'min_value.required' => '报警下限值不能为空',
            'max_value.required' => '报警上限值不能为空',
            'alarm_grade_id.required' => '报警等级ID不能为空',
            'notify_user_ids.required' => '通知用户ID不能为空',
            'type.required' => '报警类型不能为空',
        ];
    }
}
