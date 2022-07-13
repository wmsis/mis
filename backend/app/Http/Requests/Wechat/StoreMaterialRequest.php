<?php

namespace App\Http\Requests\Wechat;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
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
            'img' => 'required',
            'title' => 'required',
            'description' => 'required',
            'url' => 'required',
            'type' => 'required',
            'pic_txt_id' => 'required'
        ];
    }

    /**
     * 自定义错误消息
     */
    public function messages(){
        return [
            'img.required' => '素材图片不能为空',
            'title.required' => '素材标题不能为空',
            'description.required' => '素材描述不能为空',
            'url.required' => '素材连接不能为空',
            'type.required' => '素材类型不能为空',
            'pic_txt_id.required' => '图文消息ID不能为空',
        ];
    }
}
