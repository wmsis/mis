<?php

namespace App\Http\Requests\MIS;

use Illuminate\Foundation\Http\FormRequest;

class InspectPointStoreRequest extends FormRequest
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
            'device_id' => 'required',
            'address' => 'required'
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
            'device_id.required' => '设备ID不能为空',
            'address.required' => '地址不能为空'
        ];
    }
}
