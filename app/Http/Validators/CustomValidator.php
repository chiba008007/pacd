<?php
namespace App\Http\Validators;
 
class CustomValidator extends \Illuminate\Validation\Validator
{
    /**
     * 半角英数字、その他記号のみ許可
     * 
     * @return boolean
     */
    public function validateHankakuCheck($attribute, $value, $parameters) {
        return preg_match('/^[a-zA-Z0-9\-\\\$\_]+$/', $value);
    }

    /**
     * 半角英字のみ許可
     *
     * @return boolean
     */
    public function validateAlphaCheck($attribute, $value, $parameters)
    {
        return preg_match('/^[A-Za-z]+$/', $value);
    }
    
    /**
     * 半角英字と数字のみ許可
     *
     * @return boolean
     */
    public function validateAlphaNumCheck($attribute, $value, $parameters)
    {
        return preg_match('/^[A-Za-z\d]+$/', $value);
    }
    
    /**
     * 半角英字、数字、アンダースコア、ハイフンを許可
     *
     * @return boolean
     */
    public function validateAlphaDashCheck($attribute, $value, $parameters)
    {
        return preg_match('/^[A-Za-z\d_-]+$/', $value);
    }

    /**
     * メールアドレスの形式を許可
     * 
     * @return boolean
     */
    public function validateEmailCheck($attribute, $value, $parameters)
    {
        return preg_match('/^[a-zA-Z0-9_+-]+(.[a-zA-Z0-9_+-]+)*@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/', $value);
    }

    public function validateKanaCheck($attribute, $value, $parameters)
    {
        return preg_match('/^[ぁ-んー]+$/u', $value);
    }
}