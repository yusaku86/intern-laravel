<?php

namespace App\Http\Requests\AdminUser;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePassRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'password'              => 'required | confirmed | password',
            'password_confirmation' => 'required'
        ];
    }

    // 以下フォームに入力された値を取得するメソッド
    public function password()
    {
        return $this->input('password');
    }
}
