<x-main title="診察時間" css="{{ url(mix('css/business_hour.css')) }}">

    <?php
    // 曜日を 「英語名、日本語名」で格納した配列。曜日を表示するところと、DBから値を取得する際に使用する。
    $daysOfWeek = [['sun', '日曜日'], ['mon', '月曜日'], ['tue', '火曜日'], ['wed', '水曜日'], ['thu', '木曜日'], ['fri', '金曜日'], ['sat', '土曜日']];
    ?>

    <div class="local-container hidden">
        <form class="mt-md" id="form_business_hour" action="{{ route('business_hour') }}" method="POST">
            @csrf
            {{-- ヘッダー --}}
            <div class="header mt-md">
                <span class="header__title">診療時間</span>
                <select class="header__hospital" name="hospital">
                    @foreach ($hospitals as $hospital)
                        @if ($hospital->id === $selectedId)
                            <?php $selectedHospital = $hospital; ?>
                            <option value={{ $hospital->id }} selected>{{ $hospital->name }}</option>
                        @else
                            <option value={{ $hospital->id }}>{{ $hospital->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            {{-- メインコンテンツ --}}
            <div class="time mt-md">
                <input type="hidden" name="deleted-list" value="">
                <input type="hidden" name="changed-list" value="">
                <input type="hidden" name="added-list" value="">

                @for ($dayNum = 0; $dayNum <= 6; $dayNum++)
                    {{-- $holidayKey:その曜日が定休日かを表すカラム名(「is_open_曜日」の形、月曜日なら is_open_mon) --}}
                    <?php $holidayKey = 'is_open_' . $daysOfWeek[$dayNum][0]; ?>

                    <div class="time__item" id="time__item{{ $dayNum }}">
                        <span class="time__item-day">{{ $daysOfWeek[$dayNum][1] }}</span>
                        <span class="time__holiday radius btn-red none" id="holiday-{{ $dayNum }}">定休日</span>
                        <input type="hidden" name="is_open{{ $dayNum }}" value="{{ old('is_open' . $dayNum, $selectedHospital->$holidayKey) }}">

                        {{-- 診療時間グループ --}}
                        <div class="time__item-business_hours" id="business_hours{{ $dayNum }}">
                            <?php
                            // $counter:曜日の中で何番目の診療時間かを表す(例:日曜日の2つ目 ⇒ business_hour0-2)
                            $counter = 0;
                            ?>

                            @foreach ($businessHours as $businessHour)
                                @break($businessHour->days_of_week > $dayNum)
                                @continue($businessHour->days_of_week !== $dayNum)
                                <?php $counter++; ?>

                                {{-- 診療時間 --}}
                                <div class="time__item-business_hour" id="business_hour{{ $dayNum }}-{{ $counter }}">
                                    <input class="business_hour_id" type="hidden" name="business_hour_id{{ $dayNum }}-{{ $counter }}"
                                        value="{{ $businessHour->id }}_{{ $dayNum }}-{{ $counter }}">
                                    {{-- 診療開始時間 --}}
                                    <div class="time__item-start">
                                        <select class="time__item-hour" name="start_hour{{ $dayNum }}-{{ $counter }}">
                                            @for ($i = 1; $i <= 23; $i++)
                                                {{-- old関数の値と一致すれば選択、デフォルト値はDBの値 --}}
                                                <option value="{{ $i }}" @if ($i === (int) old('start_hour' . $dayNum . '-' . $counter, substr($businessHour->start_time, 0, 2))) selected @endif>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                        <span>時</span>

                                        <select class="time__item-minute" name="start_minute{{ $dayNum }}-{{ $counter }}">
                                            @for ($j = 0; $j <= 59; $j++)
                                                <option value="{{ $j }}" @if ($j === (int) old('start_minute' . $dayNum . '-' . $counter, substr($businessHour->start_time, 3, 2))) selected @endif>
                                                    {{ str_pad($j, 2, 0, STR_PAD_LEFT) }}
                                                </option>
                                            @endfor
                                        </select>
                                        <span>分</span>
                                    </div>

                                    <span>～</span>
                                    {{-- 診療終了時間 --}}
                                    <div class="time__item-end">
                                        <select class="time__item-hour" name="end_hour{{ $dayNum }}-{{ $counter }}">
                                            @for ($k = 1; $k <= 23; $k++)
                                                <option value="{{ $k }}" @if ($k === (int) old('end_hour' . $dayNum . '-' . $counter, substr($businessHour->end_time, 0, 2))) selected @endif>
                                                    {{ $k }}
                                                </option>
                                            @endfor
                                        </select>
                                        <span>時</span>

                                        <select class="time__item-minute" name="end_minute{{ $dayNum }}-{{ $counter }}">
                                            @for ($l = 0; $l <= 59; $l++)
                                                <option value="{{ $l }}" @if ($l === (int) old('end_minute' . $dayNum . '-' . $counter, substr($businessHour->end_time, 3, 2))) selected @endif>
                                                    {{ str_pad($l, 2, 0, STR_PAD_LEFT) }}
                                                </option>
                                            @endfor
                                        </select>
                                        <span>分</span>
                                    </div>
                                    {{-- ×ボタン --}}
                                    <svg class="time__delete" id="delete{{ $dayNum }}-{{ $counter }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z" />
                                        <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z" />
                                    </svg>
                                </div>

                                {{-- バリデーションエラーメッセージ(phpによるバリデーション) --}}
                                @error('business_hour' . $dayNum . '-' . $counter)
                                    <p class="error-message text-center">{{ $message }}</p>
                                @enderror
                            @endforeach
                        </div>
                        <div class="time__btn">
                            <button class="time__btn-add btn btn-gray" type="button" id="btn_add{{ $dayNum }}">時間を追加</button>
                            <button class="time__btn-holiday btn btn-skyblue" type="button" id="btn_holiday{{ $dayNum }}">定休日に設定</button>
                        </div>
                    </div>

                    {{-- エラーメッセージ(診療時間が上から順になっていない場合) --}}
                    @error('time_series' . $dayNum)
                        <p class="error-message text-center">{{ $message }}</p>
                    @enderror
                    <div class="time__item-line"></div>
                @endfor
            </div>
            {{-- 「保存しました」のメッセージ --}}
            @if (session('feedback_success'))
                <div class="success-message text-center">
                    <p>{{ session('feedback_success') }}</p>
                </div>
            @endif
            <div class="time__submit">
                <button class="btn btn-blue" type="submit" id="btn_submit">変更を保存する</button>
            </div>
        </form>
    </div>
    <script src="{{ url(mix('/js/business_hour.js')) }}"></script>
</x-main>
