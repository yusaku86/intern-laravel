<x-main title="アカウント情報" css="{{ url(mix('/css/add_account.css')) }}">
    <div class="local-container">
        <p class="page-title mt-lg">アカウント情報</p>

        <div class="account mt-lg">
            {{-- 入力フォーム --}}
            <form class="account__form" action=" {{ route('account.create-execute') }}" method="POST">
                @csrf
                <div class="account__form-item mt-md">
                    <span class="account__form-label">メールアドレス</span>
                    <input type="text" class="account__form-input" name="email" value="{{ old('email') }}">
                </div>
                @error('email')
                    <p class="error-message text-center">{{ $message }}</p>
                @enderror
                <div class="account__form-item">
                    <span class="account__form-label">権限</span>
                    <select class="account__form-select" name="authority">
                        <option class="text-center" value="user">一般ユーザー</option>
                        <option class="text-center" value="administrator">管理者</option>
                    </select>
                </div>
                @error('authority')
                    <p class="error-message text-center">{{ $message }}</p>
                @enderror
                <div class="account__form-item">
                    <span class="account__form-label text-center">パスワード</span>
                    <input type="password" class="account__form-input" name="password" value="{{ old('password') }}">
                </div>
                @error('password')
                    <p class="error-message text-center">{{ $message }}</p>
                @enderror
                <div class="account__form-item">
                    <span class="account__form-label">パスワード(確認)</span>
                    <input type="password" class="account__form-input" name="password_confirmation" value="{{ old('password_confirmation') }}">
                </div>
                @error('password_confirmation')
                    <p class="error-message text-center">{{ $message }}</p>
                @enderror

                {{-- 登録が完了した時のメッセージ --}}
                @if (session('feedback_success'))
                    <p class="success-message text-center">{{ session('feedback_success') }}</p>
                @endif

                <div class="account__btn text-center mt-md mb-md">
                    <button class="btn btn-blue" type="submit">アカウントを保存する</button>
                </div>
            </form>
        </div>
    </div>
</x-main>
