<?php

namespace App\Http\Requests\AdminUser;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * 権限設定
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * バリデーションルール
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'loginId'  => 'required',
            'password' =>  'required'
        ];
    }

    // 以下フォームに入力された値を返すメソッド
    public function loginId()
    {
        return $this->input('loginId');
    }

    public function password()
    {
        return $this->input('password');
    }
}
