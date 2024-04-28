<?php

namespace App\Http\Requests;

use App\Rules\CustomFormDataRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'sei' => 'required|string|max:100',
            'mei' => 'required|string|max:100',
            'sei_kana' => 'required|kana-check|max:100',
            'mei_kana' => 'required|kana-check|max:100',
            'email' => 'required|email-check|max:255',
            'remarks' => 'nullable|string',
            'custom.*' => new CustomFormDataRule(),
            'custom.*.form_input_id' => 'required_with:custom.*|exists:form_inputs,id',
            'custom.*.type' => 'required_with:custom.*|between:1,3',
            'custom.*.data' => 'nullable|array',
            'custom.*.data_sub' => 'nullable|array',
        ];

        // 管理者の場合、会員区分項目追加
        if (isCurrent('admin.*')) {
         //   $rules['type'] = ['required', 'in:'. implode(',', array_keys(config('pacd.user.type')))];
         //   $rules['is_enabled_invoice'] = 'required|boolean';
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'type' => '会員区分',
            'is_enabled_invoice' => '請求書・領収書ダウンロード',
        ];
    }
}
