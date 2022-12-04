<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ url(mix('css/login.css')) }}">
    <title>Login</title>
</head>

<body>
    <div class="global-container">
        <div class="container">
            <p class="page-title">管理画面ログイン</p>
            <div class="login">
                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="login__item">
                        <span class="login__label">ログインID</span>
                        <input type="text" class="login__input" name="loginId" value="{{ old('loginId') }} ">
                    </div>
                    @if (session('id_error'))
                        <p class="login__error error-message">{{ session('id_error') }}</p>
                    @endif

                    <div class="login__item">
                        <span class="login__label">パスワード</span>
                        <input type="password" class="login__input" name="password" value="{{ old('password') }}">
                    </div>
                    @if (session('password_error'))
                        <p class="login__error error-message">{{ session('password_error') }}</p>
                    @endif

                    @if ($errors->has('loginID') || $errors->has('password'))
                        <p class="login__error error-message">入力を行って下さい。</p>
                    @endif

                    <div class="login__btn mt-lg">
                        <button type="submit" class="btn">ログイン</button>
                    </div>

                    <div class="login__sso mt-lg text-center">
                        <div class="login__sso-title">
                            <p>外部認証でログイン(登録)</p>
                        </div>
                        <div class="login__sso-item mt-lg">
                            <div class="login__sso-item-google">
                                <a href="{{ route('login.sso', 'google') }}">Googleでログイン(登録)</a>
                            </div>
                            <div class="login__sso-item-twitter">
                                <a href="{{ route('login.sso', 'twitter') }}">Twitterでログイン(登録)</a>
                            </div>
                            <div class="login__sso-item-github">
                                <a href="{{ route('login.sso', 'github') }}">GitHubでログイン(登録)</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
