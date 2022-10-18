<x-main title="長期休暇設定" css="{{ url(mix('/css/vacation.css')) }}">
    <div class="local-container">
        <form name="vacation_form" action="{{ route('vacation.execute') }}" method="POST" class="mt-md">
            @csrf
            <div class="mt-md header">
                <span class="header__title">長期休暇設定</span>
                <input type="hidden" name="is_hospital_changed" value="">
                <select name="hospital" class="header__hospital">
                    @foreach ($hospitals as $hospital)
                        @if ($hospital->id === $selectedId)
                            <option value={{ $hospital->id }} selected>{{ $hospital->name }}</option>
                        @else
                            <option value={{ $hospital->id }}>{{ $hospital->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="vacation mt-lg">
                <div class="vacation__add">
                    <span>期間</span>
                    <div class="vacation__add-period">
                        <input class="vacation__add-start" name="add_start-date" type="date">
                        <span>～</span>
                        <input class="vacation__add-end" name="add_end-date" type="date">
                        <input class="vacation__add-reason" name="add_reason" type="text" placeholder="理由を入力">
                    </div>
                    <div class="vacation__add-btn">
                        <button type="button" class="btn btn-blue" id="btn_add">保存する</button>
                    </div>
                </div>

                <div class="vacation__list mt-lg">
                    @foreach ($vacations as $vacation)
                        <div class="vacation__list__item">
                            <div class="vacation__list__item-period">
                                <span id="start_date-{{ $vacation->id }}">{{ date('Y年n月j日', strtotime($vacation->start_date)) }}</span>
                                <span>－</span>
                                <span id="end_date-{{ $vacation->id }}">{{ date('Y年n月j日', strtotime($vacation->end_date)) }}</span>
                            </div>
                            <div class="vacation__list__item-reason" id="reason-{{ $vacation->id }}">
                                {{ $vacation->reason }}
                            </div>
                            <div class="vacation__list__item-btn">
                                <a class="btn-delete radius btn-red text-center" href="{{ route('vacation.delete', $vacation->id) }}" id="btn_delete-{{ $vacation->id }}">削除</a>
                            </div>
                        </div>
                        <div class="vacation__list-line"></div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
    <script src="{{ url(mix('/js/vacation.js')) }}"></script>
</x-main>
