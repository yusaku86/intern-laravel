<x-main title="診察時間" css="{{ url(mix('css/business_hour.css')) }}">

    <?php
    /* ---------------------------------------------------前処理----------------------------------------------------------*/
    $oldInputs = session()->get('_old_input') ?? []; // フォームに入力した値
    /**
     * 新規追加の診療時間(値がnew_0-0の形のinputタグを格納)
     * →new0-0の0-0の部分を切り取った配列$newBusinessHoursを作成
     * バリデーションに引っかかった場合・既存の診療時間を削除した場合に使用
     **/
    if ($oldInputs !== []) {
        // new_0-0の0-0部分を切り取り配列に格納
        $newBusinessHours = array_map(
            function ($value) {
                return str_replace('new_', '', $value);
            },
            // oldInputsの中で値が new_で始まるものを抽出(新規追加の診療時間:new_0-0の形)
            array_filter($oldInputs, function ($value) {
                return preg_match('/^new_\d-\d$/', $value);
            }),
        );
    } else {
        $newBusinessHours = [];
    }

    /**
     * $businessHoursには選択した病院の診療時間を曜日番号でソートしたコレクションが渡されるが、
     * for文で処理する際、キー番号の順番がばらばらのため、for文で回したときに曜日番号順になるようにキー番号を格納した配列を作成
     **/
    $sortedBusinessHours = [];
    for ($i = 0; $i < 7; $i++) {
        for ($j = 0; $j < count($businessHours); $j++) {
            if ($businessHours[$j]->days_of_week === $i) {
                // $iの値と曜日番号が一緒だったら、「$j - 曜日番号」の形で格納
                $sortedBusinessHours[] = $j . '-' . $businessHours[$j]->days_of_week;
            }
        }
    }
    // 新規追加の診療時間も含めて、曜日番号順になるように配列を作成(診療時間の情報を出力する際のキー番号として使用)
    if ($newBusinessHours === []) {
        // $newBusinessHoursが空(新規追加分がない)の場合 ⇒ $sortedBusinessHoursの 0-0 を '-' で分割し、前半部分を格納
        $keys = array_map(function ($value) {
            return (int) explode('-', $value)[0];
        }, $sortedBusinessHours);
    } else {
        $keys = [];
    }
    if ($newBusinessHours !== []) {
        foreach ($sortedBusinessHours as $sortedBusinessHour) {
            foreach ($newBusinessHours as $newBusinessHour) {
                if ((int) substr($newBusinessHour, 0, 1) >= (int) explode('-', $sortedBusinessHour)[1]) {
                    break;
                } elseif ((int) substr($newBusinessHour, 0, 1) < (int) explode('-', $sortedBusinessHour)[1]) {
                    $keys[] = $newBusinessHour;
                    $newBusinessHours = array_values(array_diff($newBusinessHours, [$newBusinessHour]));
                }
            }
            $keys[] = (int) explode('-', $sortedBusinessHour)[0];
        }
    }
    // 上記の処理では土曜日の新規追加分が配列に追加されないので土曜日の新規追加分があれば追加
    if ($newBusinessHours !== []) {
        foreach ($newBusinessHours as $saturdayBusinessHour) {
            $keys[] = $saturdayBusinessHour;
        }
    }
    // 曜日を 「英語名、日本語名」で格納した配列。曜日を表示するところと、DBから値を取得する際に使用する。
    $daysOfWeek = [['sun', '日曜日'], ['mon', '月曜日'], ['tue', '火曜日'], ['wed', '水曜日'], ['thu', '木曜日'], ['fri', '金曜日'], ['sat', '土曜日']];
    /* ---------------------------------------------------前処理----------------------------------------------------------*/
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
                <input type="hidden" name="changed-list" value="{{ old('changed-list') }}">
                <input type="hidden" name="post-type" value=""> {{-- 診療時間を削除する時のpost通信の際にバリデーションを無視するために使用 --}}

                @for ($dayNum = 0; $dayNum <= 6; $dayNum++)
                    {{-- $holidayKey:その曜日が定休日かを表すカラム名(「is_open_曜日」の形、月曜日なら is_open_mon) --}}
                    <?php $holidayKey = 'is_open_' . $daysOfWeek[$dayNum][0]; ?>
                    <div class="time__item" id="time__item{{ $dayNum }}">
                        <span class="time__item-day">{{ $daysOfWeek[$dayNum][1] }}</span>
                        <span class="time__holiday radius btn-red none" id="holiday-{{ $dayNum }}">定休日</span>
                        <input type="hidden" name="is_open{{ $dayNum }}" value="{{ old('is_open' . $dayNum, $selectedHospital->$holidayKey) }}">

                        {{-- 診療時間グループ --}}
                        <div class="time__item-business_hours" id="business_hours{{ $dayNum }}">

                            @foreach ($keys as $key)
                                <?php
                                $tmpBusinessHour = isset($businessHours[$key]) ? $businessHours[$key] : null;
                                $isNew = $tmpBusinessHour === null ? true : false;
                                ?>

                                {{-- 診療時間の曜日番号が現在のループの曜日番号($dayNum)より大きければbreak, 異なればcontinue --}}
                                @break(($isNew && (int) substr($key, 0, 1) > $dayNum) || (!$isNew && $tmpBusinessHour->days_of_week > $dayNum))
                                @continue(($isNew && (int) substr($key, 0, 1) !== $dayNum) || (!$isNew && $tmpBusinessHour->days_of_week !== $dayNum))

                                <?php
                                $businessHourNumber = $isNew ? $key : $dayNum . '-' . $tmpBusinessHour->id;
                                $newOrBlank = $isNew ? '_new' : '';
                                ?>

                                {{-- 診療時間 --}}
                                <div @if ($isNew) class="time__item-business_hour new-business_hour" @else class="time__item-business_hour" @endif
                                    id="business_hour{{ $businessHourNumber }}">
                                    <input class="business_hour_id" type="hidden" name="business_hour_id{{ $newOrBlank }}{{ $businessHourNumber }}"
                                        @if ($isNew) value="new_{{ $businessHourNumber }}" @else value="{{ $businessHourNumber }}" @endif>

                                    {{-- 診療開始時間 --}}
                                    <div class="time__item-start">
                                        <select class="time__item-hour" name="start_hour{{ $businessHourNumber }}">
                                            @if ($isNew)
                                                <option value="default" hidden>-</option>
                                            @endif
                                            @for ($j = 1; $j <= 23; $j++)
                                                {{-- old関数の値と一致すれば選択、新規追加でなければDBの値がデフォルト値 --}}
                                                <option value="{{ $j }}"
                                                    @if ($isNew && $j === (int) old('start_hour' . $businessHourNumber) && old('start_hour' . $businessHourNumber) !== 'default') selected
                                                    @elseif (!$isNew && $j === (int) old('start_hour' . $businessHourNumber, substr($tmpBusinessHour->start_time, 0, 2))) selected @endif>
                                                    {{ $j }}
                                                </option>
                                            @endfor
                                        </select>
                                        <span>時</span>

                                        <select class="time__item-minute" name="start_minute{{ $businessHourNumber }}">
                                            @if ($isNew)
                                                <option value="default" hidden>-</option>
                                            @endif
                                            @for ($k = 0; $k <= 59; $k++)
                                                <option value="{{ $k }}"
                                                    @if ($isNew && $k === (int) old('start_minute' . $businessHourNumber) && old('start_minute' . $businessHourNumber) !== 'default') selected
                                                    @elseif (!$isNew && $k === (int) old('start_minute' . $businessHourNumber, substr($tmpBusinessHour->start_time, 3, 2))) selected @endif>
                                                    {{ str_pad($k, 2, 0, STR_PAD_LEFT) }}
                                                </option>
                                            @endfor
                                        </select>
                                        <span>分</span>
                                    </div>

                                    <span>～</span>
                                    {{-- 診療終了時間 --}}
                                    <div class="time__item-end">
                                        <select class="time__item-hour" name="end_hour{{ $businessHourNumber }}">
                                            @if ($isNew)
                                                <option value="default" hidden>-</option>
                                            @endif
                                            @for ($l = 1; $l <= 23; $l++)
                                                <option value="{{ $l }}"
                                                    @if ($isNew && $l === (int) old('end_hour' . $businessHourNumber) && old('end_hour' . $businessHourNumber) !== 'default') selected
                                                    @elseif(!$isNew && $l === (int) old('end_hour' . $businessHourNumber, substr($tmpBusinessHour->end_time, 0, 2))) selected @endif>
                                                    {{ $l }}
                                                </option>
                                            @endfor
                                        </select>
                                        <span>時</span>

                                        <select class="time__item-minute" name="end_minute{{ $businessHourNumber }}">
                                            @if ($isNew)
                                                <option value="default" hidden>-</option>
                                            @endif
                                            @for ($m = 0; $m <= 59; $m++)
                                                <option value="{{ $m }}"
                                                    @if ($isNew && $m === (int) old('end_minute' . $businessHourNumber) && old('end_minute' . $businessHourNumber) !== 'default') selected
                                                    @elseif (!$isNew && $m === (int) old('end_minute' . $businessHourNumber, substr($tmpBusinessHour->end_time, 3, 2))) selected @endif>
                                                    {{ str_pad($m, 2, 0, STR_PAD_LEFT) }}
                                                </option>
                                            @endfor
                                        </select>
                                        <span>分</span>
                                    </div>
                                    {{-- ×ボタン --}}
                                    <svg class="time__delete" id="delete{{ $businessHourNumber }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z" />
                                        <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z" />
                                    </svg>
                                </div>

                                {{-- バリデーションエラーメッセージ(phpによるバリデーション) --}}
                                @if ($errors->has('business_hour' . $businessHourNumber) || isset($oldInputs['error_' . $businessHourNumber]))
                                    {{-- 診療時間を削除し、画面が遷移してもエラーメッセージを引き継ぐためのinputタグ --}}
                                    <p class="error-message text-center">{{ old('error_' . $businessHourNumber, $errors->first('business_hour' . $businessHourNumber)) }}</p>
                                    <input type="hidden" name="error_{{ $businessHourNumber }}"
                                        value="{{ old('error_' . $businessHourNumber, $errors->first('business_hour' . $businessHourNumber)) }}">
                                @endif
                            @endforeach
                        </div>
                        <div class="time__btn">
                            <button class="time__btn-add btn btn-gray" type="button" id="btn_add{{ $dayNum }}">時間を追加</button>
                            <button class="time__btn-holiday btn btn-skyblue" type="button" id="btn_holiday{{ $dayNum }}">定休日に設定</button>
                        </div>
                    </div>

                    {{-- エラーメッセージ(診療時間が上から順になっていない場合) --}}
                    @if ($errors->has('time_series' . $dayNum) || isset($oldInputs['error_' . $dayNum]))
                        {{-- 診療時間を削除し、画面が遷移してもエラーメッセージを引き継ぐためのinputタグ --}}
                        <p class="error-message text-center">{{ old('error_' . $dayNum, $errors->first('time_series' . $dayNum)) }}</p>
                        <input type="hidden" name="error_{{ $dayNum }}" value="{{ old('error_' . $dayNum, $errors->first('time_series' . $dayNum)) }}">
                    @endif
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
