<?php

namespace App\Http\Controllers\AdminUser;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUser\CreateRequest;
use App\Models\Admin_user;

class CreateController extends Controller
{
    // ユーザーの追加
    function __invoke(CreateRequest $request)
    {
        Admin_user::create([
            'email' => $request->email(),
            'password' => password_hash($request->password, PASSWORD_DEFAULT)
        ]);

        return redirect()->route('account.create')->with('feedback_success', '登録が完了しました。');
    }
}
