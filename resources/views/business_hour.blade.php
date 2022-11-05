<x-main title="診察時間" css="{{ url(mix('css/business_hour.css')) }}">

    <?php
    // $businessHoursには選択した病院の診療時間を曜日番号で昇順に並び変えたコレクションを配列に変換したものが渡される

    $oldInputs = session()->get('_old_input') ?? [];

    // 新規追加の診療時間(バリデーションに引っかかった場合・既存の診療時間を削除した場合に使用)
    // oldInputsの中で値が new_で始まるものを抽出(新規追加の診療時間:new_0-0の形)
    $newBusinessHours = array_filter($oldInputs, function ($value) {
        return preg_match('/^new_\d-\d$/', $value);
    });
    /**
     * 診療時間の配列($businessHours)の末尾に新規追加分を追加
     * id                   ⇒ 新規かどうかを判断するため、また、日付番号で並び替えをする際に同じ日付番号の時は新規分があとに来るよう new という文字列を使用
     * business_hour_number ⇒ htmlエレメントの名前やidに使用する。「日付番号 - 曜日内の新規で何個目か」の形。例:日曜日で2つめの新規 → 0-2
     * days_of_week         ⇒ 日付番号
     **/
    foreach ($newBusinessHours as $newBusinessHour) {
        $businessHours[] = [
            'id'                   => 'new',
            'business_hour_number' => str_replace('new_', '', $newBusinessHour),
            'days_of_week'         => (int) substr($newBusinessHour, 4, 1),
        ];
    }
    // 日付番号とidで昇順に並び替え
    $daysOfWeekColumn = array_column($businessHours, 'days_of_week');
    $idColumn         = array_column($businessHours, 'id');

    array_multisort($daysOfWeekColumn, SORT_ASC, $idColumn, SORT_ASC, $businessHours);

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

                            @foreach ($businessHours as $businessHour)
                                <?php $isNew = $businessHour['id'] === 'new' ? true : false; ?>

                                {{-- 診療時間の曜日番号が現在のループの曜日番号($dayNum)より大きければbreak, 異なればcontinue --}}
                                @break($businessHour['days_of_week'] > $dayNum)
                                @continue($businessHour['days_of_week'] !== $dayNum)

                                <?php
                                $businessHourNumber = $isNew ? $businessHour['business_hour_number'] : $dayNum . '-' . $businessHour['id'];
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
                                                    @elseif (!$isNew && $j === (int) old('start_hour' . $businessHourNumber, substr($businessHour['start_time'], 0, 2))) selected @endif>
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
                                                    @elseif (!$isNew && $k === (int) old('start_minute' . $businessHourNumber, substr($businessHour['start_time'], 3, 2))) selected @endif>
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
                                                    @elseif(!$isNew && $l === (int) old('end_hour' . $businessHourNumber, substr($businessHour['end_time'], 0, 2))) selected @endif>
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
                                                    @elseif (!$isNew && $m === (int) old('end_minute' . $businessHourNumber, substr($businessHour['end_time'], 3, 2))) selected @endif>
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
