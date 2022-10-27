<x-main title="Home" css="{{ url(mix('/css/home.css')) }}">
    <div class="local-container">
        <div class="header mt-lg">
            <span class="header__title">ホーム</span>
            <div class="header__user">
                <span class="header__user-id">{{ Auth::user()->email }}</span>
                さん
            </div>
            <div class="header__btn">
                <button class="btn"><a href="{{ route('logout') }}">ログアウト</a></button>
            </div>
        </div>

        <div class="home">
            <a class="home__item" href="{{ route('business_hour') }}">診察時間編集</a>
            <a class="home__item" href="{{ route('vacation') }}">長期休業設定</a>

            @can('administrator')
                <a class="home__item" href="{{ route('account.index') }}">アカウント情報</a>
                <a class="home__item" href="{{ route('hospital') }}">病院登録</a>
                <a class="home__item" href="{{ route('download.index') }}">CSVダウンロード</a>
            @endcan
        </div>
    </div>
</x-main>
