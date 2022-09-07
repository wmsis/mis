<?php

namespace App\Http\Requests\API\Equipment;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentSparePartRequest extends FormRequest
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
            'serial_number' => 'required',
            'name' => 'required',
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
            'serial_number.required' => 'serial_number 不能为空',
            'name.required' => 'name 不能为空',
        ];
    }
}
