<x-main title="アカウント情報" css="{{ url(mix('/css/account_index.css')) }}">
    <div class="local-container">
        <div class="header mt-lg">
            <span class="header__title">アカウント情報</span>
        </div>

        {{-- メインコンテンツ --}}
        <div class="account mt-lg">
            <div class="account__header">
                <span class="account__header-item">メールアドレス</span>
                <span class="account__header-item ml-sm">権限</span>
            </div>
            <div class="account__list">
                @foreach ($accounts as $account)
                    <div class="account__list-item">
                        <span class="account__list-email" id="email-{{ $account->id }}">{{ $account->email }}</span>
                        <span class="account__list-authority" id="authority-{{ $account->id }}">
                            @if ($account->authority === 'administrator')
                                管理者
                            @elseif ($account->authority === 'user')
                                一般ユーザー
                            @endif
                        </span>
                        <div class="account__list-btn">
                            <a class="btn-password radius btn-gray" type="button" href="{{ route('account.change_pass', $account->id) }}">パスワード変更</a>
                            <a class="btn-delete radius btn-red" type="button" id="btn_delete-{{ $account->id }}" href="{{ route('account.delete', $account->id) }}">削除</a>
                        </div>
                    </div>
                    <div class="account__list-line"></div>
                @endforeach
                <div class="account__btn mt-md">
                    <a class="btn-add btn btn-blue" href="{{ route('account.create') }}">アカウントを追加する</a>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ url(mix('/js/account_index.js')) }}"></script>
</x-main>
