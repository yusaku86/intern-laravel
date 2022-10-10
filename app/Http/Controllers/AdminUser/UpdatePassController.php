<?php

namespace App\Http\Controllers\AdminUser;

use App\Http\Controllers\Controller;
use App\Models\Admin_user;
use App\Http\Requests\AdminUser\UpdatePassRequest;

class UpdatePassController extends Controller
{
    public function showPage($id, $feedback = null)
    {
        $account = Admin_user::find($id);
        return view('account_change_pass')
            ->with([
                'account' => $account,
                'feedback' => $feedback
            ]);
    }

    public function updatePass(UpdatePassRequest $request, $id)
    {
        Admin_user::find($id)->update([
            'password' => password_hash($request->password, PASSWORD_DEFAULT),
        ]);

        return $this->showPage($id, 'パスワードを変更しました。');
    }
}
