window.addEventListener('DOMContentLoaded', function () {
    new VacationsController();
})

class VacationsController {

    constructor() {
        this.form = document.querySelector('form[name="vacation_form"]');
        this.addStartDate = document.querySelector('input[name="add_start-date"]');     // 追加する長期休暇の開始日を入力するinputタグ
        this.addEndDate = document.querySelector('input[name="add_end-date"]');         // 追加する長期休暇の終了日を入力するinputタグ
        this.addReason = document.querySelector('input[name="add_reason"]');            // 追加する長期休暇の理由を入力するinputタグ
        this.deleteTarget = document.querySelector('input[name="delete_target"]');      // 削除する長期休暇のidを値としてもつinputタグ(hidden)

        this.selectHospital = document.querySelector('select[name="hospital"]');        // 病院を選択するSelectタグ
        this.selectHospital.addEventListener('change', this.changeHospital.bind(this));

        const addBtn = document.querySelector('#btn_add');
        addBtn.addEventListener('click', this.addVacation.bind(this));

        const deleteBtns = document.querySelectorAll('.btn-delete');
        deleteBtns.forEach(deleteBtn => deleteBtn.addEventListener('click', (event) => this.deleteVacation(deleteBtn, event)));

    }


    /**
     * 長期休暇追加実行前の確認 ⇒ 期間と理由が全て入力してあれば実行する
     */
    addVacation() {
        if (this.addStartDate.value !== '' && this.addEndDate.value !== '' && this.addReason.value !== '') {
            this.form.submit();
        }
    }

    /**
     * 長期休暇の削除
     */
    deleteVacation($deleteBtn, event) {
        const targetid = $deleteBtn.id.split('-').pop();
        const startDate = document.querySelector(`#start_date-${targetid}`);
        const endDate = document.querySelector(`#end_date-${targetid}`);
        const reason = document.querySelector(`#reason-${targetid}`);

        if (!window.confirm(`「${startDate.innerHTML}～${endDate.innerHTML} ${reason.innerHTML.replace(/\r?\n/g, '').trim()}」を削除します。よろしいですか?`)) {
            event.preventDefault();
        }
    }

    /**
     * 病院の変更
     */
    changeHospital() {
        window.location.href = `/vacation/${this.selectHospital.value}`;
    }
}
