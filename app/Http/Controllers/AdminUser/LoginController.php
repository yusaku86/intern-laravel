<?php

namespace App\Http\Controllers\AdminUser;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminUser\LoginRequest;
use App\Models\Admin_user;

class LoginController extends Controller
{
    // ログイン画面表示
    public function showLoginPage()
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
