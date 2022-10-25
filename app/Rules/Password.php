<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Password implements Rule
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
        // 大小アルファベット・記号が含まれるか判定
        return preg_match('/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[!-\/:-@[-`{-~])[!-~]{3,100}+\z/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'パスワードは大小アルファベット・記号を含めてください';
    }
}
