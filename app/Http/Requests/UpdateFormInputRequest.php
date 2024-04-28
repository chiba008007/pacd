<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UpdateFormInputRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'form_type' => 'required|integer|between:1,7',
            'name' => 'required|string|max:255',
            'validation_required' => 'boolean',
            'validation_numeric' => 'boolean',
            'validation_alpha' => 'boolean',
            'is_display_published' => 'boolean',
            'is_display_user_list' => 'boolean',
            'type' => 'required|integer|between:1,3',
            'count' => 'required|integer|min:1',
            'value.*' => 'nullable|string',
            'csvtag' => 'regex:/^[0-9]+$/',
        ];
        if ($this->validation_required || $this->validation_numeric || $this->validation_alpha) {
            // エラーチェックがある場合はエラーメッセージ必須
            $rules['validation_message'] = 'required|string';
        } else {
            $rules['validation_message'] = 'nullable|string';
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => '項目名',
            'validation_message' => 'エラーメッセージ',
            'count' => '入力枠数または選択項目数',
            'value.*' => '入力例または選択項目',
            'csvtag' => 'CSV出力箇所',
        ];
    }

    public function messages()
    {
        return [
            'validation_message.required' => 'エラーチェックを行う場合は必ず指定してください。',
        ];
    }

    // バリデーション実行後のデータ修正
    public function validationData() {
        // validation_rulesカラム追加
        if ($this->validation_required) {
            $rules[] = 'required';
        }
        if ($this->validation_numeric && $this->validation_alpha) {
            $rules[] = 'alpha-num-check';
        } elseif ($this->validation_numeric) {
            $rules[] = 'numeric';
        } elseif ($this->validation_alpha) {
            $rules[] = 'alpha-check';
        }
        $this->merge([
            'validation_rules' => isset($rules) ? implode('|', $rules) : '',
        ]);

        return $this->all();
    }
}
