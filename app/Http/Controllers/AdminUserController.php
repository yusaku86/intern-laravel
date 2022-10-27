<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUser\CreateRequest;
use App\Http\Requests\AdminUser\UpdatePassRequest;
use App\Models\Admin_user;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminUser\LoginRequest;


class AdminUserController extends Controller
{
    // ユーザー一覧表示
    public function index()
    {
        $accounts = Admin_user::all();

        return view('admin_user')->with('accounts', $accounts);
    }

    // ユーザーの追加
    function create(CreateRequest $request)
    {
        Admin_user::create([
            'email' => $request->email(),
            'authority' => $request->authority(),
            'password' => password_hash($request->password, PASSWORD_DEFAULT)
        ]);

        return redirect()->route('account.create')->with('feedback_success', '登録が完了しました。');
    }

    // ユーザーの削除
    public function delete($id)
    {
        Admin_user::destroy($id);

        return redirect()->route('account.index');
    }

    // ユーザー追加ページ表示
    public function indexAddPage()
    {
        return view('add_account');
    }

    // ユーザーパスワード編集画面表示
    public function indexChangePass($id, $feedback = null)
    {
        $account = Admin_user::find($id);
        return view('account_change_pass')
            ->with([
                'account' => $account,
                'feedback' => $feedback
            ]);
    }

    // ユーザーパスワード変更
    public function updatePass(UpdatePassRequest $request, $id)
    {
        Admin_user::find($id)->update([
            'password' => password_hash($request->password, PASSWORD_DEFAULT),
        ]);

        return $this->indexChangePass($id, 'パスワードを変更しました。');
    }

    // ログイン画面表示
    public function indexLoginPage()
    {
        return view('login');
    }

    // ログイン認証
    public function login(LoginRequest $request)
    {
        $inputLoginId = $request->loginId;
        $inputPassword = $request->password;

        // ログインIDが存在するか
        if (!Admin_user::where('email', '=', $inputLoginId)->exists()) {
            return redirect()->route('login')->with('id_error', 'ログインIDが存在しません。')->withInput();
        }

        $userInfo = [
            'email'    => $inputLoginId,
            'password' => $inputPassword
        ];

        // パスワードが一致するか
        if (!Auth::attempt($userInfo)) {
            return redirect()->back()->with('password_error', 'パスワードが間違っています。')->withInput();
        }

        $request->session()->regenerate(); // セッション固定攻撃対策
        return redirect()->route('home');
    }

    // ログアウト
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerate();
        return redirect()->route('login');
    }
}
