<x-main title="パスワード変更" css="{{ url(mix('/css/account_pass.css')) }}">
    <div class="local-container">
        <div class="header mt-lg">
            <span class="header__title">アカウント情報</span>
        </div>
        <form action="{{ route('account.change_pass_execute', $account->id) }}" method="POST" class="account mt-md">
            @csrf
            <div class="account__item">
                <span class="account__item-header">メールアドレス</span>
                <span class="account__item-content">{{ $account->email }}</span>
            </div>
            <div class="account__item">
                <span class="account__item-header">パスワード</span>
                <input type="password" class="account__item-content" name="password">
            </div>
            @error('password')
                <p class="error-message text-center">{{ $message }}</p>
            @enderror
            <div class="account__item">
                <span class="account__item-header">パスワード(確認)</span>
                <input type="password" class="account__item-content" name="password_confirmation">
            </div>
            @error('password_confirmation')
                <p class="error-message text-center">{{ $message }}</p>
            @enderror
            @if ($feedback)
                <p class="success-message text-center">{{ $feedback }} </p>
            @endif

            <div class="text-center mt-md">
                <button type="submit" class="btn btn-blue">パスワードを保存する</button>
            </div>
        </form>
    </div>
</x-main>
