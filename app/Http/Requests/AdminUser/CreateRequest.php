<?php

namespace App\Http\Requests\AdminUser;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Password;

class CreateRequest extends FormRequest
{
    /**
     * 権限の設定
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * バリデーションのルール
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email'                 => 'required | email | unique:admin_users',
            'password'              => 'required | confirmed | password',
            'password_confirmation' => 'required'
        ];
    }

    // 以下フォームに入力された値を取得するメソッド
    public function email()
    {
        return $this->input('email');
    }

    public function password()
    {
        return $this->input('password');
    }
}
