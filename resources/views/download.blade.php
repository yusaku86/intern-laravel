<x-main title="CSVダウンロード" css="{{ url(mix('/css/download.css')) }}">
    <div class="local-container">
        <p class="page-title mt-lg">CSVダウンロード</p>
        <div class="download">
            <p class="download__title mt-md">診療時間ダウンロード</p>
            <div class="download__item">
                <select name="hospital-businesshour" class="download__item-hospital">
                    @foreach ($hospitals as $hospital)
                        <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                    @endforeach
                </select>
                <div class="download__item-btn">
                    <button class="download__item-btn-download btn btn-green" id="btn-businesshour" type="button">ダウンロード</a>
                </div>
            </div>
            <p class="download__title">長期休暇ダウンロード</p>
            <div class="download__item">
                <select name="hospital-vacation" class="download__item-hospital">
                    @foreach ($hospitals as $hospital)
                        <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                    @endforeach
                </select>
                <div class="download__item-btn">
                    <button class="download__item-btn-download btn btn-green" id="btn-vacation" type="button">ダウンロード</button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ url(mix('/js/download_csv.js')) }}"></script>
</x-main>
