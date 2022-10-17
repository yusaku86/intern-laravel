window.addEventListener('DOMContentLoaded', function () {
    // ページの読み込み終わってから表示
    document.querySelector('.local-container').classList.remove('hidden');

    new BusinessHoursController();
})

class BusinessHoursController {

    constructor() {
        // 定休日に設定されている曜日のスタイル反映
        this.loadHolidays();

        // 「時間を追加」ボタンにイベント設定
        const addBtns = document.querySelectorAll('.time__btn-add');
        this.setEvent(addBtns, 'click', this.addBusinessHour.bind(this), true);

        // ×ボタンにイベント設定
        const deleteBtns = document.querySelectorAll('.time__delete');
        this.setEvent(deleteBtns, 'click', this.deleteBusinessHour, true);

        // 「定休日に設定」ボタンにイベント設定
        const holidayBtns = document.querySelectorAll('.time__btn-holiday');
        this.setEvent(holidayBtns, 'click', this.clickHolidayBtn.bind(this), true);

        // 時間と分を選択するselectタグにイベント設定
        const timeSelects = document.querySelectorAll('.time__item-hour, .time__item-minute');
        this.setEvent(timeSelects, 'change', this.addChangedList, true);

        // 病院のSelectタグにイベント設定
        this.hospitalSelect = document.querySelector('select[name="hospital"]');
        this.hospitalSelect.addEventListener('change', this.changeHospital.bind(this));
    }

    // 画面読み込み時の定休日の設定
    loadHolidays() {
        for (let i = 0; i <= 6; i++) {
            if (document.querySelector(`input[name="is_open${i}"]`).value === '0') {
                this.setHoliday(document.querySelector(`#btn_holiday${i}`).id.slice(-1));
            }
        }
    }

    /**
     * 診療時間の追加
     * @param addBtn「時間を追加」ボタンElement
     */
    addBusinessHour(addBtn) {
        // 曜日番号(0～6)
        const dayOfWeek = addBtn.id.slice(-1);

        // 診療時間を追加するグループ
        const targetBusinessHours = document.querySelector(`#business_hours${dayOfWeek}`);

        // 追加時のID番号と新しいidやnameで使うもの
        const newIdNumber = Number(targetBusinessHours.lastElementChild.id.split('-').pop()) + 1;
        const newId = `${dayOfWeek}-${newIdNumber}`;

        // 診療時間を1組コピー
        const cloneElement = targetBusinessHours.children[0].cloneNode(true);
        cloneElement.id = `business_hour${newId}`;

        // 削除ボタン
        const cloneDeleteBtn = cloneElement.querySelector('.time__delete');
        cloneDeleteBtn.classList.remove('hidden');
        cloneDeleteBtn.id = `delete${newId}`;
        this.setEvent(cloneDeleteBtn, 'click', this.deleteBusinessHour);

        // 時間のSelectタグ(startとendの2つ)の名前とIDの変更とデフォルト値設定
        const cloneSelectHours = cloneElement.querySelectorAll('.time__item-hour');
        this.setSelectElement(cloneSelectHours, 'hour', newId);

        // 分のSelectタグ(startとendの2つ)の名前とIDの変更とデフォルト値設定
        const cloneSelectMinutes = cloneElement.querySelectorAll('.time__item-minute');
        this.setSelectElement(cloneSelectMinutes, 'minute', newId);

        //「診療時間のDB上のidを表すinputタグ => 値を空にする
        const cloneBusinessHourId = cloneElement.querySelector('.business_hour_id');
        cloneBusinessHourId.name = `business_hour_id${newId}`;
        cloneBusinessHourId.value = '';

        //added-list(新規追加リスト)に値を追加
        const addedList = document.querySelector('input[name="added-list"]');
        addedList.value += `${newId},`;

        targetBusinessHours.appendChild(cloneElement);
    }

    /**　追加分の時間と分のSelectタグの名前とID変更、デフォルト値設定 */
    setSelectElement(selectElements, timeType, newId) {
        selectElements.forEach(selectElement => {
            this.changeSelectName(selectElement, selectElement.name.split('_')[0], timeType, newId);
            selectElement.insertBefore(this.createDefalutOption(), selectElement.firstChild);
        })
    }

    /**
     * 追加分の時間と分のセレクトタグの名前変更
     * @params timeType 時間か分のどちらか
     * @params timeOption startかendのどちらか
     *  */
    changeSelectName(selectElement, timeOption, timeType, newId) {
        selectElement.name = `${timeOption}_${timeType}${newId}`;
    }

    /**追加分の時間と分のデフォルト値のOptionタグの作成 */
    createDefalutOption() {
        const defalutOption = document.createElement('option');
        defalutOption.value = 'default';
        defalutOption.innerText = '-';
        defalutOption.selected = true;
        defalutOption.hidden = true;
        return defalutOption;
    }

    /**
     * 診療時間の削除
     * @param deleteBtn ×ボタンElement
     */
    deleteBusinessHour(deleteBtn) {
        // 曜日番号(0～6)
        const dayOfWeek = deleteBtn.closest('.time__item-business_hours').id.slice(-1);
        // クエリセレクタ―で使用するid名
        const targetId = `${dayOfWeek}-${deleteBtn.id.split('-').pop()}`;

        // 削除対象の診療時間
        const deleteTarget = document.querySelector(`#business_hour${targetId}`);
        // 削除対象のDB上のidを表すinputタグ
        const businessHourId = document.querySelector(`input[name="business_hour_id${targetId}"]`);

        // 削除対象が既存のものだったら削除リストに追加、新規追加したものを削除する場合は追加リストから削除
        if (businessHourId.value !== '') {
            const deletedList = document.querySelector('input[name="deleted-list"]');
            deletedList.value += `${businessHourId.value},`;
        } else {
            const addedList = document.querySelector('input[name="added-list"]');
            addedList.value = addedList.value.replace(`${targetId},`, '');
        }

        deleteTarget.remove();
    }

    /**
     * 定休日ボタンクリック時
     * @param holidayBtn「定休日に設定」ボタン
     */
    clickHolidayBtn(holidayBtn) {
        const dayOfWeek = holidayBtn.id.slice(-1);

        this.setHoliday(dayOfWeek);

        // 定休日かどうかを表すinputタグの値を変更
        const isOpen = document.querySelector(`input[name="is_open${dayOfWeek}"]`);
        if (isOpen.value === 'true' || isOpen.value === '1') {
            isOpen.value = 'false';
        } else {
            isOpen.value = 'true';
        }
    }

    /**
     * 定休日設定
     * @params dayOfWeek 曜日番号
     */
    setHoliday(dayOfWeek) {
        // 診療時間グループ
        const targetBusinessHours = document.querySelector(`#business_hours${dayOfWeek}`);
        targetBusinessHours.classList.toggle('none');

        // 定休日のタグ
        const targetHoliday = document.querySelector(`#holiday-${dayOfWeek}`);
        targetHoliday.classList.toggle('none');

        // 定休日ボタンの表示文字変更
        if (targetHoliday.classList.contains('none')) {
            document.querySelector(`#btn_holiday${dayOfWeek}`).innerHTML = '定休日に設定';
        } else {
            document.querySelector(`#btn_holiday${dayOfWeek}`).innerHTML = '定休日を解除';
        }

        // 「時間を追加」ボタン
        const addBtn = document.querySelector(`#btn_add${dayOfWeek}`);
        addBtn.classList.toggle('hidden');
    }

    /**
     * 時間が変更された時の処理
     * @param changedElement 時間が変更されたSelectタグ
     */
    addChangedList(changedElement) {
        // 時間が変更された曜日番号
        const dayOfWeek = changedElement.closest('.time__item-business_hours').id.slice(-1);
        // 変更された診療時間のDB上のid
        const businessHourId = document.querySelector(`input[name="business_hour_id${dayOfWeek}-${changedElement.name.split('-').pop()}"]`).value;
        // 変更された診療時間のリスト
        const changedList = document.querySelector('input[name="changed-list"]');

        // リストに値がなければ追加
        if (changedList.value === '' || !changedList.value.split(',').includes(businessHourId)) {
            changedList.value += `${businessHourId},`;
        }
    }

    /**
     * 病院の変更
     */
    changeHospital() {
        window.location.href = `/business_hour/${this.hospitalSelect.value}`;
    }

    // イベントリスナー設定
    setEvent(elements, event, callback, isArray = false) {
        if (isArray) {
            elements.forEach(element => element.addEventListener(event, () => callback(element)));
        } else {
            elements.addEventListener(event, () => callback(elements));
        }
    }
}
