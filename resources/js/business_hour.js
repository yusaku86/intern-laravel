const { error } = require("laravel-mix/src/Log");

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

        // 各曜日内に診療時間がに5つあったらそれ以上追加できないようにする
        for (let i = 0; i < 7; i++) {
            this.countBusinessHours(i);
        }
        // 「変更を保存」ボタンにイベント設定
        // document.querySelector('#btn_submit').addEventListener('click', this.validate.bind(this));
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
        const newId = this.getNewId(targetBusinessHours);

        // 診療時間を1組コピー
        const cloneElement = targetBusinessHours.children[0].cloneNode(true);
        cloneElement.id = `business_hour_new${newId}`;
        cloneElement.classList.add("new-business_hour");

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

        //「診療時間のDB上のidを表すinputタグ => 値をnew_0-0の形に変更
        const cloneBusinessHourId = cloneElement.querySelector('.business_hour_id');
        cloneBusinessHourId.name = `business_hour_id_new${newId}`;
        cloneBusinessHourId.value = `new_${newId}`;

        targetBusinessHours.appendChild(cloneElement);

        // 曜日内で診療時間が5個になったらそれ以上追加できなくする
        this.countBusinessHours(dayOfWeek);
    }

    // 新規追加する診療時間を示す番号を取得(「曜日番号」-「曜日の中で何番目か」の形 例:0-1)
    getNewId(targetBusinessHours) {
        const dayOfWeek = parseInt(targetBusinessHours.id.replace('business_hours', ''));
        let countNew = 0;

        for (let i = 0; i < targetBusinessHours.children.length; i++) {
            if (targetBusinessHours.children[i].classList.contains('new-business_hour')) countNew++;
        }
        return `${dayOfWeek}-${countNew + 1}`;
    }

    /**　追加分の時間と分のSelectタグの名前とID変更、デフォルト値設定 */
    setSelectElement(selectElements, timeType, newId) {
        selectElements.forEach(selectElement => {
            this.changeSelectName(selectElement, selectElement.name.split('_')[0], timeType, newId);
            selectElement.insertBefore(this.createDefalutOption(), selectElement.firstChild);
        })
    }

    /**
     * 曜日内に診療時間が5つあったらそれ以上追加できないようにする
     */
    countBusinessHours(dayOfWeek) {
        const targetBusinessHours = document.querySelector(`#business_hours${dayOfWeek} `);
        let result = 0;
        for (let i = 0; i < targetBusinessHours.children.length; i++) {
            if (targetBusinessHours.children[i].classList.contains('time__item-business_hour')) result++
        }
        if (result >= 5) {
            document.querySelector(`#btn_add${dayOfWeek} `).disabled = true;
        }
        return;
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

        // 削除対象の診療時間(新規追加の場合は business_hour_new${targetId});
        let newName = document.querySelector(`#business_hour${targetId} `) == null ? '_new' : '';
        const deleteTarget = document.querySelector(`#business_hour${newName}${targetId} `);

        // 削除対象のDB上のidを表すinputタグ(新規追加分はinputタグのnameが business_hour_id_new${targetId})
        newName = document.querySelector(`input[name="business_hour_id${targetId}"]`) == null ? '_new' : '';
        const businessHourId = document.querySelector(`input[name="business_hour_id${newName}${targetId}"]`);

        // 削除対象が既存のものだったらDBから削除、新規追加したものを削除する場合は画面から消去(Laravelのバリデーションが走らないように post-typeのvalueをdeleteに変更)
        if (businessHourId.value.substr(0, 4) !== 'new_') {
            document.querySelector('input[name="post-type"]').value = "delete";

            const form = document.querySelector('#form_business_hour');
            form.action = `/business_hour/delete/${businessHourId.value.split('-')[1]}`;
            form.submit();
            return;
        }

        // 削除対象にエラーメッセージが表示されていた場合エラーメッセージも削除
        if (deleteTarget.nextElementSibling !== null && deleteTarget.nextElementSibling.classList.contains('error-message')) {
            deleteTarget.nextElementSibling.remove();
        }
        deleteTarget.remove();

        // 「時間を追加」ボタンを有効化
        document.querySelector(`#btn_add${dayOfWeek} `).disabled = false;
    }

    /**
     * 定休日ボタンクリック時
     * @param holidayBtn「定休日に設定」ボタン
     */
    clickHolidayBtn(holidayBtn) {
        const dayOfWeek = holidayBtn.id.slice(-1);

        this.setHoliday(dayOfWeek);

        // 定休日かどうかを表すinputタグの値を変更
        const isOpen = document.querySelector(`input[name = "is_open${dayOfWeek}"]`);
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
        // 診療時間グループを非表示(表示)
        const targetBusinessHours = document.querySelector(`#business_hours${dayOfWeek}`);
        targetBusinessHours.classList.toggle('none');

        // 定休日のタグを非表示(表示)
        const targetHoliday = document.querySelector(`#holiday-${dayOfWeek}`);
        targetHoliday.classList.toggle('none');

        // 定休日ボタンの表示文字変更
        if (targetHoliday.classList.contains('none')) {
            document.querySelector(`#btn_holiday${dayOfWeek} `).innerHTML = '定休日に設定';
        } else {
            document.querySelector(`#btn_holiday${dayOfWeek} `).innerHTML = '定休日を解除';
        }

        // 「時間を追加」ボタンの非表示(表示)
        const addBtn = document.querySelector(`#btn_add${dayOfWeek} `);
        addBtn.classList.toggle('hidden');

        // エラーメッセージがあれば非表示
        const nextElement = document.querySelector(`#time__item${dayOfWeek} `).nextElementSibling;
        if (nextElement.classList.contains('error-message')) nextElement.classList.toggle('none');
    }

    /**
     * 時間が変更された時の処理
     * @param changedElement 時間が変更されたSelectタグ
     */
    addChangedList(changedElement) {
        // 時間か分か
        const timeType = changedElement.classList.contains('time__item-hour') ? 'hour' : 'minute';
        // 変更された診療時間「曜日番号」-「DBのid」
        const businessHourId = changedElement.name.split(timeType)[1];
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

    /**
     * 診療時間のバリデーション
     * PHPでバリデーションした場合、新規の診療時間がバリデーションに引っかかるとDBに保存されない⇒
     * 画面読み込み時にDBからデータを引っ張るためバリデーションに引っかかった新規診療時間が消えてしまうので、jsで先に行う
     */
    validate(event) {
        // ダブルクリック防止
        document.querySelector('#btn_submit').classList.add('pointer-none');
        // 開始時間と終了時間のバリデーション
        const invalidBusinessHoursNumber = this.validateBusinessHours();
        let invalidBusinessHour;
        let isError = false;

        // 開始時間と終了時間のバリデーションに引っかかったもののエラーメッセージ表示
        if (invalidBusinessHoursNumber.length > 0) {
            invalidBusinessHoursNumber.forEach(invalidBusinessHourNumber => {
                invalidBusinessHour = document.querySelector(`#business_hour${invalidBusinessHourNumber}`) ??
                    document.querySelector(`#business_hour_new${invalidBusinessHourNumber}`);

                this.showErrorMessage(invalidBusinessHour, 'businessHour');
                isError = true;
            });
        }

        // 曜日内で診療時間が時系列に並んでいるかのバリデーション
        document.querySelectorAll('.time__item-business_hours').forEach(businessHourGroup => {
            if (this.validateTimeSeries(businessHourGroup) === false) {
                this.showErrorMessage(document.querySelector(`#time__item${businessHourGroup.id.slice(-1)} `), 'timeSeries');
                isError = true;
            }
        });

        if (isError === true) {
            event.preventDefault();
            document.querySelector('#btn_submit').classList.remove('pointer-none');
        }
    }
    /**
     * 診療時間の開始時間と終了時間のバリデーションを全て行い、引っかかったものをinvalidBusinessHoursに格納
     */
    validateBusinessHours() {
        const businessHours = document.querySelectorAll('.business_hour_id');
        const invalidBusinessHours = [];
        let businessHourNumber

        businessHours.forEach(businessHour => {
            businessHourNumber = businessHour.name.replace('business_hour_id', '').replace('_new', '');

            if (!this.validateBusinessHour(businessHourNumber)) {
                invalidBusinessHours.push(businessHourNumber);
            }
        });
        return invalidBusinessHours;
    }

    /**
     * 診療時間の開始時間と終了時間のバリデーション
     * @param businessHourId 診療時間を識別するためのid:「曜日番号」-曜日の中で何番目か」の形(例:0-1)
     */
    validateBusinessHour(businessHourId) {
        const startHour = document.querySelector(`select[name = "start_hour${businessHourId}"]`);
        const startMinute = document.querySelector(`select[name = "start_minute${businessHourId}"]`);
        const endHour = document.querySelector(`select[name = "end_hour${businessHourId}"]`);
        const endMinute = document.querySelector(`select[name = "end_minute${businessHourId}"]`);

        // 診療時間が新規追加されたもので、時間が選択されていないものは飛ばす
        // それ以外は終了時間が開始時間より前の場合にfalseを返す
        if (startHour.value === 'default' || startMinute.value === 'default' || endHour.value === 'default' || endMinute.value === 'default') {
            return true;
        } else if (60 * parseInt(startHour.value) + parseInt(startMinute.value) >= 60 * parseInt(endHour.value) + parseInt(endMinute.value)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 曜日内で診療時間が上から時系列に設定されているか確認
     * @params targetBusinessHourGroup 確認する診療時間グループ
     */
    validateTimeSeries(targetBusinessHourGroup) {
        const targetBusinessHours = [];
        let startHour;
        let startMinute;
        let endHour;
        let endMinute;
        let businessHourNumber;
        let result = true;

        Array.from(targetBusinessHourGroup.children).forEach(businessHour => {
            if (!businessHour.classList.contains('time__item-business_hour')) return;

            businessHourNumber = businessHour.id.replace('business_hour', '').replace('_new', '');

            startHour = document.querySelector(`select[name="start_hour${businessHourNumber}"]`).value;
            startMinute = document.querySelector(`select[name = "start_minute${businessHourNumber}"]`).value;
            endHour = document.querySelector(`select[name = "end_hour${businessHourNumber}"]`).value;
            endMinute = document.querySelector(`select[name = "end_minute${businessHourNumber}"]`).value;

            if (startHour === 'default' || startMinute === 'default' || endHour === 'default' || endMinute === 'default') return;

            if (targetBusinessHourGroup.children[0] !== businessHour) targetBusinessHours.push(60 * parseInt(startHour) + parseInt(startMinute));
            targetBusinessHours.push(60 * parseInt(endHour) + parseInt(endMinute));
        })

        for (let i = 0; i < targetBusinessHours.length; i = i + 2) {
            if (i < targetBusinessHours.length - 1 && targetBusinessHours[i] >= targetBusinessHours[i + 1]) {
                result = false;
                return result;
            }
        }
        return result;
    }

    // エラーメッセージ表示
    showErrorMessage(targetElement, option) {
        // 既にエラーメッセージが表示されている場合は抜ける
        if (targetElement.nextElementSibling !== null && targetElement.nextElementSibling.classList.contains('error-message')) return;

        const errorP = document.createElement('p');
        const errorInput = document.createElement('input');

        errorP.classList.add('error-message');
        errorP.classList.add('text-center');
        errorInput.type = 'hidden';

        if (option === 'businessHour') {
            errorP.innerHTML = '終了時間は開始時間より後に設定してください。';
            errorInput.value = '終了時間は開始時間より後に設定してください。';
            errorInput.name = `error_${targetElement.id.replace('business_hour', '').replace('_new', '')}`;
        } else if (option === 'timeSeries') {
            errorP.innerHTML = '各曜日の診療時間は上から時系列で設定してください。';
            errorInput.value = '各曜日の診療時間は上から時系列で設定してください。';
            errorInput.name = `error_${targetElement.id.slice(-1)}`;
        }
        targetElement.after(errorInput);
        targetElement.after(errorP);

        // 「保存しました」のメッセージが表示されている場合は削除
        const successMessage = document.querySelector('.success-message');
        if (successMessage !== null && successMessage.classList.contains('success-message')) successMessage.remove();
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
