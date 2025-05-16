<?php

namespace App\Rules;

use App\Models\FormInput;
use Illuminate\Contracts\Validation\Rule;

class CustomFormDataRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        if (!is_array($value) || !isset($value['form_input_id'])) {
            return false;
        }


        $this->data = FormInput::find($value['form_input_id']);

        if (!$this->data) {
            return false;
        }
        $rules = [];
        if ($this->data) {
            // プルダウン、複数選択型の場合、データはIDで持つため必須のみチェック
            if (strpos($this->data->validation_rules, 'required') !== false) {
                $rules['data'] = 'required';
                
                // テクストボックス付き複数選択型ので必須の場合、テキストボックスが入力されているか
                if (isset($value['data_sub'])) {
                    foreach($value['data_sub'] as $form_input_values_id => $val) {
                        if (isset($value['data'][$form_input_values_id]))
                            return !empty($val);
                    }
                }
            }
            
            // テキスト型の場合すべてのバリデーション実行
            /*
            if ($this->data->type == config('pacd.form.input_type.text')) {
                $rules['data.*'] = $this->data->validation_rules;
            }
            */
            if (isset($rules)) {
                return validator($value, $rules)->passes();
            }
        }
        
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if (isset($this->data)) {
            return $this->data->validation_message ?? ' ';
        }
        return ' ';
    }
}
