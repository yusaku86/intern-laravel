<x-main title="病院登録" css="{{ url(mix('/css/hospital.css')) }}">
    <div class="local-container">
        <p class="page-title mt-lg">病院登録</p>
        <div class="hospital">
            <form class="hospital__form" action="{{ route('hospital.create') }}" method="POST">
                @csrf
                <div class="hospital__form-item mt-md">
                    <span class="hospital__form-label">病院名</span>
                    <input type="text" class="hospital__form-input" name="hospital_name">
                </div>
                @error('hospital_name')
                    <p class="error-message text-center">{{ $message }}</p>
                @enderror
                @if (session('feedback_success'))
                    <p class="success-message text-center">{{ session('feedback_success') }}</p>
                @endif
                <div class="text-center mt-md mb-md">
                    <button class="btn btn-blue" type="submit">病院を登録する</button>
                </div>
            </form>
        </div>
    </div>
</x-main>
