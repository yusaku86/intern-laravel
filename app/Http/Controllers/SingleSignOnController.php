<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin_user;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SingleSignOnController extends Controller
{
    // SSOでログイン(新規登録)するための画面表示
    public function showSsoLoginPage(string $resourceServer)
    {
        return Socialite::driver($resourceServer)->redirect();
    }

    // SSOでログイン(新規登録)
    public function executeSso($resourceServer, Request $request)
    {
        // SSOログインをキャンセルした場合
        if ($request->query('denied') !== null || $request->query('error') !== null) {
            return redirect()->route('home');
        }

        $ssoUser = Socialite::driver($resourceServer)->user();

        if ($resourceServer === 'google') {
            $user = $this->searchOrCreateGoogleUser($ssoUser);
        } elseif ($resourceServer === 'twitter') {
            $user = $this->searchOrCreateTwitterUser($ssoUser);
        } elseif ($resourceServer === 'github') {
            $user = $this->searchOrCreateGitHubUser($ssoUser);
        }

        Auth::login($user);

        return redirect()->route('home');
    }


    /**
     * 複数のリソースプロバイダを利用してログインする場合があるが(GoogleとTwitterの両方でログインなど)、
     * メールアドレスは重複して登録できないため、登録されているメールアドレスを持つアカウントがSSOでログインした場合には、
     * 既存のユーザーのリソースプロバイダidの値を登録する(GoogleIdやTwitterIdのこと)
     */

    // emailでユーザーを検索 ⇒ 登録されていればgoogle_idの値を登録、いなければ新規作成
    private function searchOrCreateGoogleUser($googleUser)
    {
        $user = Admin_user::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $user->update(['google_id' => $googleUser->getId()]);
        } else {
            $user = Admin_user::create([
                'email'     => $googleUser->getEmail(),
                'authority' => 'administrator',
                'google_id' => $googleUser->getid(),
            ]);
        }
        return $user;
    }

   // emailでユーザーを検索 ⇒ 登録されていればtwitter_idの値を登録、いなければ新規作成
    private function searchOrCreateTwitterUser($twitterUser)
    {
        $user = Admin_user::where('email', $twitterUser->getEmail())->first();

        if ($user) {
            $user->update(['twitter_id' => $twitterUser->getid()]);
        } else {
            $user = Admin_user::create([
                'email'      => $twitterUser->getEmail(),
                'authority'  => 'administrator',
                'twitter_id' => $twitterUser->getid(),
            ]);
        }
        return $user;
    }

       // emailでユーザーを検索 ⇒ 登録されていればtwitter_idの値を登録、いなければ新規作成
    private function searchOrCreateGitHubUser($gitHubUser)
    {
        $user = Admin_user::where('email', $gitHubUser->getEmail())->first();

        if ($user) {
            $user->update(['github_id' => $gitHubUser->getid()]);
        } else {
            $user = Admin_user::create([
                'email'      => $gitHubUser->getEmail(),
                'authority'  => 'administrator',
                'github_id' => $gitHubUser->getid(),
            ]);
        }
        return $user;
    }
}
