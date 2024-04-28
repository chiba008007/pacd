<?php

namespace App\Http\Requests;

use App\Rules\CustomFormDataRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

class CreateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [

            'login_id' => 'required|min:4|max:50|unique:users',
            'sei' => 'required|string|max:100',
            'mei' => 'required|string|max:100',
            'sei_kana' => 'required|kana-check|max:100',
            'mei_kana' => 'required|kana-check|max:100',
            'email' => 'required|email-check|max:255',
            /*
            'type_number' => 'required',

            'cp_name' => 'required',
            'tel' => 'required',
            'address' => 'required',
            'busyo' => 'required',
            */
            'password' => 'required|alpha-num-check|min:4|max:16|confirmed',
            //'remarks' => 'nullable|string',

            'custom.*' => new CustomFormDataRule(),
            'custom.*.form_input_id' => 'required_with:custom.*|exists:form_inputs,id',
            'custom.*.type' => 'required_with:custom.*|between:1,3',
            'custom.*.data' => 'nullable|array',
            'custom.*.data_sub' => 'nullable|array',

        ];
        
        $rules['is_enabled_invoice'] = 'required|boolean';


        // 管理者の場合、会員区分、メール送信有無項目追加
        if (isCurrent('admin.*')) {
            $rules['type'] = ['required', 'in:'. implode(',', array_keys(config('pacd.user.type')))];
            $rules['is_enabled_invoice'] = 'required|boolean';
            $rules['send_mail'] = ['nullable', 'boolean'];
        }
        //公開画面からの登録時に会員区分が会員外のときは「法人会員番号」「法人名」のチェックを外す
        if (preg_match("/register$/",url()->current())) {
            if(Request::input('type') == 1){
                unset($rules['type_number']);
                unset($rules['cp_name']);
            }
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
